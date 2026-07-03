<?php

namespace App\Services\Tracking;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookCapiService
{
    /**
     * Determine if the Conversions API is fully configured and enabled.
     */
    public static function isEnabled(): bool
    {
        $config = config('extension.facebook_pixel');
        
        return !empty($config['is_enabled']) && 
               !empty($config['pixel_id']) && 
               !empty($config['access_token']);
    }

    /**
     * Send a Purchase event to the Meta Conversions API.
     */
    public static function sendPurchase(
        Order $order, 
        ?string $ip = null, 
        ?string $userAgent = null,
        ?string $fbp = null,
        ?string $fbc = null
    ): void
    {
        if (!self::isEnabled()) {
            return;
        }

        $config = config('extension.facebook_pixel');
        $pixelId = $config['pixel_id'];
        $accessToken = $config['access_token'];
        $testCode = $config['test_event_code'] ?? null;

        // Collect request details if not explicitly passed
        $ip = $ip ?? request()->ip();
        $userAgent = $userAgent ?? request()->userAgent();
        $fbp = $fbp ?? request()->cookie('_fbp');
        $fbc = $fbc ?? request()->cookie('_fbc');

        // Format user details (Meta requires SHA-256 hashed lowercase trimmed values)
        $userData = [];
        
        if ($order->customer_email) {
            $userData['em'] = [self::hashValue($order->customer_email)];
        }
        
        if ($order->customer_phone) {
            $userData['ph'] = [self::hashPhone($order->customer_phone)];
        }
        
        if ($order->customer_name) {
            // Meta takes first name (fn) and last name (ln) separately. Let's send the first name part.
            $parts = explode(' ', trim($order->customer_name));
            $firstName = $parts[0] ?? '';
            if ($firstName) {
                $userData['fn'] = [self::hashValue($firstName)];
            }
            if (isset($parts[1])) {
                $userData['ln'] = [self::hashValue(end($parts))];
            }
        }

        if ($ip && !in_array($ip, ['127.0.0.1', '::1'])) {
            $userData['client_ip_address'] = $ip;
        }
        if ($userAgent) {
            $userData['client_user_agent'] = $userAgent;
        }
        if ($fbp) {
            $userData['fbp'] = $fbp;
        }
        if ($fbc) {
            $userData['fbc'] = $fbc;
        }

        // Format custom event items data
        $contents = [];
        foreach ($order->items as $item) {
            $contents[] = [
                'id' => (string) $item->product_id,
                'quantity' => (int) $item->quantity,
                'item_price' => (float) $item->price
            ];
        }

        $customData = [
            'currency' => getOption('currency_code', 'BDT'),
            'value' => (float) $order->total,
            'content_type' => 'product',
            'contents' => $contents
        ];

        // Format the complete event payload
        $eventData = [
            'event_name' => 'Purchase',
            'event_time' => time(),
            'event_id' => $order->order_number, // Must match client event ID for deduplication
            'event_source_url' => route('shop.checkout.confirmation', $order->order_number),
            'action_source' => 'website',
            'user_data' => $userData,
            'custom_data' => $customData,
        ];

        $payload = [
            'data' => [$eventData],
            'access_token' => $accessToken
        ];

        // If a test event code is configured, append it to verify in Events Manager
        if (!empty($testCode)) {
            $payload['test_event_code'] = $testCode;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("https://graph.facebook.com/v19.0/{$pixelId}/events", $payload);

            if ($response->failed()) {
                Log::error('Meta Conversions API Error: ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('Meta Conversions API Exception: ' . $e->getMessage());
        }
    }

    /**
     * SHA-256 hash formatting helper.
     */
    private static function hashValue(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        return hash('sha256', strtolower(trim($value)));
    }

    /**
     * Bangladesh country-code formatted phone hashing helper.
     */
    private static function hashPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }
        // Remove all non-numeric characters
        $clean = preg_replace('/[^0-9]/', '', $phone);
        // Prepend country code '88' if it starts with '01' (standard Bangladeshi mobile number)
        if (strlen($clean) === 11 && str_starts_with($clean, '01')) {
            $clean = '88' . $clean;
        }
        return hash('sha256', $clean);
    }
}
