<?php

namespace App\Http\Controllers\Shop;

use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Ecommerce\OrderNotifier;
use App\Services\Payment\SslCommerzService;
use Illuminate\Http\Request;

/**
 * Handles SSLCommerz redirect/IPN callbacks for storefront orders.
 *
 * The order is created (unpaid/pending) before the customer is sent to the
 * gateway. Here we validate the transaction with SSLCommerz and, on success,
 * flip it to paid + confirmed. Failed/cancelled orders are left as-is (an admin
 * can cancel them to restore stock).
 */
class PaymentController extends Controller
{
    public function __construct(private SslCommerzService $sslcommerz) {}

    /** Customer redirected back after a successful payment. */
    public function success(Request $request)
    {
        $order = $this->sslcommerz->validateCallback($request);

        if (! $order) {
            return redirect()->route('shop.cart.index')
                ->with('error', __('We could not verify your payment. Please contact support.'));
        }

        $this->markPaid($order, $request);

        session()->put('confirmed_order', $order->order_number);

        return redirect()->route('shop.checkout.confirmation', $order->order_number)
            ->with('success', __('Payment successful. Thank you for your order!'));
    }

    /** Customer redirected back after a failed payment. */
    public function failed(Request $request)
    {
        $orderNumber = $request->input('tran_id');

        return redirect()->route('shop.cart.index')
            ->with('error', __('Your payment failed. Your order :order is awaiting payment — you can try again or choose Cash on Delivery.', ['order' => $orderNumber]));
    }

    /** Customer cancelled at the gateway. */
    public function cancel(Request $request)
    {
        return redirect()->route('shop.cart.index')
            ->with('error', __('Payment was cancelled. Your cart has been kept.'));
    }

    /**
     * Server-to-server IPN. SSLCommerz calls this directly (no browser session),
     * so it just validates and marks paid; the response body is irrelevant to it.
     */
    public function ipn(Request $request)
    {
        $order = $this->sslcommerz->validateCallback($request);

        if ($order) {
            $this->markPaid($order, $request);

            return response('IPN OK', 200);
        }

        return response('IPN INVALID', 200);
    }

    /**
     * Re-start the gateway for an order that was placed for online payment but
     * never paid (customer failed or cancelled). The order, amount and items are
     * unchanged — we just generate a fresh gateway session for the same order.
     */
    public function retry(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Only the registered owner, the session that placed it, or an admin may retry.
        $isOwner = auth()->check() && auth()->id() === $order->user_id;
        $isSessionBuyer = session('confirmed_order') === $order->order_number;
        $isAdmin = auth('admin')->check();

        if (! $isOwner && ! $isSessionBuyer && ! $isAdmin) {
            abort(403, __('You do not have permission to pay for this order.'));
        }

        if (! SslCommerzService::isEnabled() || ! $order->isOnlinePayable()) {
            return redirect()->route('shop.checkout.confirmation', $order->order_number)
                ->with('error', __('This order can no longer be paid online.'));
        }

        try {
            $url = $this->sslcommerz->initiate($order);

            session()->put('confirmed_order', $order->order_number);

            return redirect()->away($url);
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('shop.checkout.confirmation', $order->order_number)
                ->with('error', __('Online payment could not be started. Please try again later.'));
        }
    }

    /**
     * Flip an order to paid + confirmed exactly once, sending the order-placed
     * email on the first confirmation. Idempotent across the success + IPN paths.
     */
    private function markPaid(Order $order, Request $request): void
    {
        if ($order->payment_status === OrderPaymentStatusEnum::PAID) {
            return;
        }

        $order->update([
            'payment_status' => OrderPaymentStatusEnum::PAID->value,
            'status' => OrderStatusEnum::CONFIRMED->value,
            'transaction_id' => $request->input('val_id'),
            'gateway_transaction_id' => $request->input('bank_tran_id'),
        ]);

        OrderNotifier::orderPlaced($order);
    }
}
