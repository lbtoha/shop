<?php

namespace App\Services\Ecommerce;

use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Exceptions\CustomWebException;
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
    public function placeOrder(array $details, ?int $userId = null, float $shippingCost = 0): Order
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            throw new CustomWebException(__('Your cart is empty.'), 422);
        }

        return DB::transaction(function () use ($items, $details, $userId, $shippingCost) {
            $subtotal = 0;
            $orderItems = [];

            foreach ($items as $line) {
                /** @var Product $product */
                $product = Product::query()->lockForUpdate()->find($line['product']->id);

                if (! $product || ! $product->is_active) {
                    throw new CustomWebException(__('A product in your cart is no longer available.'), 422);
                }

                if (! $product->isInStock($line['quantity'])) {
                    throw new CustomWebException(
                        __(':product is out of stock.', ['product' => $product->name]),
                        422
                    );
                }

                $lineSubtotal = (float) $product->price * $line['quantity'];
                $subtotal += $lineSubtotal;

                $product->decrement('stock', $line['quantity']);

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $line['quantity'],
                    'subtotal' => $lineSubtotal,
                ];
            }

            $order = Order::create([
                'order_number' => UniqueCodeGenerator::make(Order::class, 'order_number', 6, 'ORD'),
                'user_id' => $userId,
                'customer_name' => $details['customer_name'],
                'customer_phone' => $details['customer_phone'],
                'customer_email' => $details['customer_email'] ?? null,
                'shipping_address' => $details['shipping_address'],
                'city' => $details['city'] ?? null,
                'zip_code' => $details['zip_code'] ?? null,
                'note' => $details['note'] ?? null,
                'payment_method' => 'cash_on_delivery',
                'payment_status' => OrderPaymentStatusEnum::UNPAID->value,
                'status' => OrderStatusEnum::PENDING->value,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $subtotal + $shippingCost,
            ]);

            $order->items()->createMany($orderItems);

            $this->cart->clear();

            return $order;
        });
    }
}
