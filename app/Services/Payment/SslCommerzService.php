<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Services\Payment\SSLCommerz\SslCommerzNotification;
use Illuminate\Http\Request;

/**
 * Thin SSLCommerz integration for the storefront.
 *
 * Currency is fixed to BDT (this shop is single-currency). Credentials and the
 * enable/test toggles are stored as Options and managed from the admin
 * Payment Settings page (admin.settings.payment).
 */
class SslCommerzService
{
    /** Admin enabled the gateway and it has usable credentials. */
    public static function isEnabled(): bool
    {
        return (int) getOption('sslcommerz_enabled', 0) === 1
            && filled(getOption('sslcommerz_store_id'))
            && filled(getOption('sslcommerz_store_password'));
    }

    /** Sandbox vs live. */
    public static function isTestMode(): bool
    {
        return (int) getOption('sslcommerz_test_mode', 1) === 1;
    }

    /** Path to the bundled default logo, shown until the admin uploads their own. */
    public const DEFAULT_LOGO = '/images/payment/sslcommerz.png';

    /**
     * Logo shown next to the "Pay Online" option at checkout. Returns the admin's
     * uploaded logo when set, otherwise the bundled default.
     */
    public static function logo(): string
    {
        $logo = getOption('sslcommerz_logo');

        return filled($logo) ? $logo : self::DEFAULT_LOGO;
    }

    private function client(): SslCommerzNotification
    {
        $sslc = new SslCommerzNotification(
            (string) getOption('sslcommerz_store_id'),
            (string) getOption('sslcommerz_store_password'),
            self::isTestMode()
        );

        $sslc->setSuccessUrl(route('shop.payment.sslcommerz.success'));
        $sslc->setFailedUrl(route('shop.payment.sslcommerz.failed'));
        $sslc->setCancelUrl(route('shop.payment.sslcommerz.cancel'));
        $sslc->setIPNUrl(route('shop.payment.sslcommerz.ipn'));

        return $sslc;
    }

    /**
     * Initiate a hosted payment session for an order.
     *
     * @return string The SSLCommerz gateway page URL to redirect the customer to.
     *
     * @throws \Exception when the gateway rejects the request (bad credentials, etc.)
     */
    public function initiate(Order $order): string
    {
        // The order number doubles as the transaction id; it is unique and lets
        // the callback resolve the order without exposing the primary key.
        $data = [
            'total_amount' => (float) $order->total,
            'currency' => 'BDT',
            'tran_id' => $order->order_number,

            'cus_name' => $order->customer_name,
            'cus_email' => $order->customer_email ?: 'guest@'.parse_url(config('app.url'), PHP_URL_HOST),
            'cus_phone' => $order->customer_phone,
            'cus_add1' => $order->shipping_address,
            'cus_city' => $order->city ?: 'Dhaka',
            'cus_country' => 'Bangladesh',

            'shipping_method' => 'NO',
            'num_of_item' => $order->items()->count(),

            'product_name' => 'Order '.$order->order_number,
            'product_category' => 'general',
            'product_profile' => 'physical-goods',
        ];

        $response = $this->client()->makePayment($data, 'hosted');

        if (empty($response['GatewayPageURL'])) {
            throw new \Exception(__('Could not start the online payment. Please try again or choose Cash on Delivery.'));
        }

        return $response['GatewayPageURL'];
    }

    /**
     * Validate a gateway callback (success / IPN) against the SSLCommerz API and
     * resolve the matching order. Returns null if validation fails or the order
     * is unknown — callers treat null as a failed payment.
     */
    public function validateCallback(Request $request): ?Order
    {
        $tranId = $request->input('tran_id');

        if (! $tranId || $request->input('status') !== 'VALID') {
            return null;
        }

        $order = Order::where('order_number', $tranId)->first();

        if (! $order) {
            return null;
        }

        $valid = $this->client()->orderValidate(
            $request->all(),
            $tranId,
            (float) $order->total,
            'BDT'
        );

        return $valid === true ? $order : null;
    }
}
