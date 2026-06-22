<?php

namespace App\Services\Ecommerce;

use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Exceptions\CustomWebException;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Services\Helper\UniqueCodeGenerator;
use Illuminate\Support\Facades\DB;

/**
 * Places cash-on-delivery orders.
 *
 * No payment gateway is involved: the order is recorded as unpaid and payment
 * is collected on delivery. Stock is decremented atomically inside a
 * transaction with row locking so concurrent checkouts cannot oversell.
 */
class CheckoutService
{
    public function __construct(private Cart $cart) {}

    /**
     * @param  array{customer_name: string, customer_phone: string, customer_email?: ?string, shipping_address: string, city?: ?string, zip_code?: ?string, note?: ?string}  $details
     *
     * @throws CustomWebException when the cart is empty or a line is out of stock
     */
    public function placeOrder(array $details, ?int $userId = null, float $shippingCost = 0, string $paymentMethod = 'cash_on_delivery'): Order
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            throw new CustomWebException(__('Your cart is empty.'), 422);
        }

        $couponCode = $this->cart->couponCode();

        return DB::transaction(function () use ($items, $details, $userId, $shippingCost, $couponCode, $paymentMethod) {
            $subtotal = 0;
            $orderItems = [];

            foreach ($items as $line) {
                /** @var Product $product */
                $product = Product::query()->lockForUpdate()->find($line['product']->id);

                if (! $product || ! $product->isActive()) {
                    throw new CustomWebException(__('A product in your cart is no longer available.'), 422);
                }

                $variant = null;

                if (! empty($line['variant']?->id)) {
                    // Lock and re-check the chosen variant; stock lives on it.
                    $variant = \App\Models\ProductVariant::query()
                        ->lockForUpdate()
                        ->where('product_id', $product->id)
                        ->find($line['variant']->id);

                    if (! $variant) {
                        throw new CustomWebException(__('A product option in your cart is no longer available.'), 422);
                    }

                    if (! $variant->isInStock($line['quantity'])) {
                        throw new CustomWebException(
                            __(':product (:variant) is out of stock.', ['product' => $product->name, 'variant' => $variant->name]),
                            422
                        );
                    }

                    $unitPrice = $variant->price();
                    $variant->decrement('stock', $line['quantity']);
                    $product->decrement('stock', $line['quantity']);
                } else {
                    if (! $product->isInStock($line['quantity'])) {
                        throw new CustomWebException(
                            __(':product is out of stock.', ['product' => $product->name]),
                            422
                        );
                    }

                    $unitPrice = (float) $product->price;
                    $product->decrement('stock', $line['quantity']);
                }

                $lineSubtotal = $unitPrice * $line['quantity'];
                $subtotal += $lineSubtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'variant_name' => $variant?->name,
                    'price' => $unitPrice,
                    'quantity' => $line['quantity'],
                    'subtotal' => $lineSubtotal,
                ];
            }

            // Resolve and lock the coupon (if any) so concurrent checkouts can't
            // exceed its usage limit. Re-validate against the freshly computed
            // subtotal; an invalid coupon is silently dropped rather than failing
            // the whole order.
            $coupon = null;
            $discount = 0;

            if ($couponCode) {
                $coupon = Coupon::query()->whereRaw('LOWER(code) = ?', [mb_strtolower(trim($couponCode))])->lockForUpdate()->first();

                if ($coupon && $coupon->isRedeemable($subtotal)) {
                    $discount = $coupon->discountFor($subtotal);
                } else {
                    $coupon = null;
                }
            }

            $order = Order::create([
                'order_number' => UniqueCodeGenerator::make(Order::class, 'order_number', 6, 'ORD'),
                'user_id' => $userId,
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
                'customer_name' => $details['customer_name'],
                'customer_phone' => $details['customer_phone'],
                'customer_email' => $details['customer_email'] ?? null,
                'shipping_address' => $details['shipping_address'],
                'city' => $details['city'] ?? null,
                'zip_code' => $details['zip_code'] ?? null,
                'note' => $details['note'] ?? null,
                'payment_method' => $paymentMethod,
                'payment_status' => OrderPaymentStatusEnum::UNPAID->value,
                'status' => OrderStatusEnum::PENDING->value,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'total' => max(0, $subtotal - $discount) + $shippingCost,
            ]);

            if ($coupon) {
                $coupon->increment('used_count');
            }

            $order->items()->createMany($orderItems);

            $this->cart->clear();

            return $order;
        });
    }
}
