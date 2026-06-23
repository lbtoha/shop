<?php

namespace App\Services\Ecommerce;

use App\Exceptions\CustomWebException;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

/**
 * Steadfast Courier Limited integration (Bangladesh COD courier).
 *
 * Talks to the Steadfast merchant API to create a consignment for an order,
 * poll its delivery status, and read the merchant's current COD balance.
 * Credentials and the enabled flag are stored as Options (admin Settings →
 * Steadfast Courier) so nothing lives in .env.
 *
 * API docs: https://steadfast.com.bd/user/api  (base portal.packzy.com/api/v1)
 */
class SteadfastService
{
    private const DEFAULT_BASE_URL = 'https://portal.packzy.com/api/v1';

    public static function isEnabled(): bool
    {
        return (int) getOption('steadfast_enabled', 0) === 1
            && getOption('steadfast_api_key')
            && getOption('steadfast_secret_key');
    }

    /**
     * Whether orders should be dispatched to Steadfast automatically when they
     * reach the configured trigger status (requires the integration enabled).
     */
    public static function isAutoSendEnabled(): bool
    {
        return self::isEnabled() && (int) getOption('steadfast_auto_send', 0) === 1;
    }

    /**
     * The order status that triggers automatic consignment creation.
     */
    public static function autoSendStatus(): string
    {
        return getOption('steadfast_auto_send_status') ?: 'processing';
    }

    private static function baseUrl(): string
    {
        return rtrim(getOption('steadfast_base_url') ?: self::DEFAULT_BASE_URL, '/');
    }

    private static function client()
    {
        return Http::baseUrl(self::baseUrl())
            ->acceptJson()
            ->timeout(30)
            ->withHeaders([
                'Api-Key' => getOption('steadfast_api_key'),
                'Secret-Key' => getOption('steadfast_secret_key'),
            ]);
    }

    /**
     * Create a Steadfast consignment for the order and persist the returned
     * consignment id / tracking code / status on the order.
     *
     * @throws CustomWebException on misconfiguration or an API failure.
     */
    public static function createConsignment(Order $order): Order
    {
        if (! self::isEnabled()) {
            throw new CustomWebException(__('Steadfast courier is not configured. Set it up in Settings → Steadfast Courier.'), 422);
        }

        if ($order->courier_consignment_id) {
            throw new CustomWebException(__('A Steadfast consignment already exists for this order.'), 422);
        }

        $payload = [
            'invoice' => $order->order_number,
            'recipient_name' => $order->customer_name,
            'recipient_phone' => preg_replace('/[^0-9]/', '', $order->customer_phone),
            'recipient_address' => trim($order->shipping_address.', '.$order->city.($order->zip_code ? ' - '.$order->zip_code : '')),
            // COD amount is the full order total for unpaid orders, 0 once paid.
            'cod_amount' => $order->payment_status->value === 'paid' ? 0 : (float) $order->total,
            'note' => $order->note ?: '',
        ];

        try {
            $response = self::client()->post('/create_order', $payload);
        } catch (\Throwable $e) {
            report($e);
            throw new CustomWebException(__('Could not reach Steadfast. Please try again.'), 422);
        }

        $data = $response->json();

        // Steadfast returns status 200 (int) on success with a `consignment` object.
        if (! $response->successful() || ($data['status'] ?? null) != 200 || empty($data['consignment'])) {
            $message = $data['message'] ?? __('Steadfast rejected the request.');
            throw new CustomWebException(__('Steadfast: :msg', ['msg' => $message]), 422);
        }

        $consignment = $data['consignment'];

        $order->update([
            'courier_consignment_id' => $consignment['consignment_id'] ?? null,
            'courier_tracking_code' => $consignment['tracking_code'] ?? null,
            'courier_status' => $consignment['status'] ?? 'in_review',
        ]);

        return $order->fresh();
    }

    /**
     * Refresh and persist the delivery status for an order that already has a
     * consignment. Returns the current status string, or null when unavailable.
     */
    public static function refreshStatus(Order $order): ?string
    {
        if (! self::isEnabled() || ! $order->courier_consignment_id) {
            return null;
        }

        try {
            $response = self::client()->get('/status_by_cid/'.$order->courier_consignment_id);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }

        $status = $response->json('delivery_status');

        if ($status) {
            $order->update(['courier_status' => $status]);
        }

        return $status;
    }

    /**
     * Delivery statuses that are final — no point polling Steadfast again.
     */
    public const TERMINAL_STATUSES = ['delivered', 'partial_delivered', 'cancelled', 'returned'];

    /**
     * Refresh the delivery status of every order that has an open (non-terminal)
     * Steadfast consignment. Returns the number of orders synced. Used by the
     * scheduled `steadfast:sync` command.
     */
    public static function syncOpenConsignments(int $limit = 100): int
    {
        if (! self::isEnabled()) {
            return 0;
        }

        $orders = Order::query()
            ->whereNotNull('courier_consignment_id')
            ->where(function ($q) {
                $q->whereNull('courier_status')
                    ->orWhereNotIn('courier_status', self::TERMINAL_STATUSES);
            })
            ->limit($limit)
            ->get();

        $synced = 0;

        foreach ($orders as $order) {
            if (self::refreshStatus($order) !== null) {
                $synced++;
            }
        }

        return $synced;
    }

    /**
     * Current merchant COD balance, or null if it can't be fetched.
     */
    public static function balance(): ?float
    {
        if (! self::isEnabled()) {
            return null;
        }

        try {
            $response = self::client()->get('/get_balance');
        } catch (\Throwable $e) {
            report($e);

            return null;
        }

        $balance = $response->json('current_balance');

        return is_numeric($balance) ? (float) $balance : null;
    }

    /**
     * Verify the given credentials (falling back to the saved ones) by calling
     * /get_balance. Returns ['ok' => bool, 'message' => string] for the admin
     * "Test connection" button. Does not require the integration to be enabled.
     */
    public static function testConnection(?string $apiKey = null, ?string $secretKey = null, ?string $baseUrl = null): array
    {
        $apiKey = $apiKey ?: getOption('steadfast_api_key');
        $secretKey = $secretKey ?: getOption('steadfast_secret_key');
        $baseUrl = rtrim(($baseUrl ?: getOption('steadfast_base_url')) ?: self::DEFAULT_BASE_URL, '/');

        if (! $apiKey || ! $secretKey) {
            return ['ok' => false, 'message' => __('Enter both the Api-Key and Secret-Key first.')];
        }

        try {
            $response = Http::baseUrl($baseUrl)
                ->acceptJson()
                ->timeout(20)
                ->withHeaders(['Api-Key' => $apiKey, 'Secret-Key' => $secretKey])
                ->get('/get_balance');
        } catch (\Throwable $e) {
            report($e);

            return ['ok' => false, 'message' => __('Could not reach Steadfast. Check the base URL and your connection.')];
        }

        if ($response->status() === 401 || $response->json('status') === 401) {
            return ['ok' => false, 'message' => __('Invalid Api-Key or Secret-Key.')];
        }

        $balance = $response->json('current_balance');

        if (! $response->successful() || ! is_numeric($balance)) {
            return ['ok' => false, 'message' => __('Steadfast rejected the request. Please re-check your credentials.')];
        }

        return ['ok' => true, 'message' => __('Connected. Current COD balance: :amount', ['amount' => amountWithSymbol((float) $balance)])];
    }
}
