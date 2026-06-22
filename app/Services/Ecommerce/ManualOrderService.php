<?php

namespace App\Services\Ecommerce;

use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Exceptions\CustomWebException;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Helper\UniqueCodeGenerator;
use Illuminate\Support\Facades\DB;

/**
 * Places orders entered by an admin (e.g. a WhatsApp or phone order) rather than
 * through the storefront cart/checkout.
 *
 * Mirrors CheckoutService's guarantees — runs in a transaction with row locking
 * so concurrent stock changes can't oversell, snapshots product/variant
 * name+price into OrderItem, and generates the order number the same way — but
 * takes an explicit item list instead of the session cart. Cash on delivery only.
 */
class ManualOrderService
{
    /**
     * @param  array{customer_name: string, customer_phone: string, customer_email?: ?string, shipping_address: string, city?: ?string, zip_code?: ?string, note?: ?string}  $details
     * @param  array<int, array{product_id: int, variant_id?: ?int, quantity: int}>  $items
     *
     * @throws CustomWebException when no items are given or a line is out of stock
     */
    public function placeOrder(array $details, array $items, float $shippingCost = 0): Order
    {
        $items = array_values(array_filter($items, fn ($line) => ! empty($line['product_id']) && (int) ($line['quantity'] ?? 0) > 0));

        if (empty($items)) {
            throw new CustomWebException(__('Add at least one product to the order.'), 422);
        }

        return DB::transaction(function () use ($items, $details, $shippingCost) {
            $subtotal = 0;
            $orderItems = [];

            foreach ($items as $line) {
                $quantity = (int) $line['quantity'];

                /** @var Product|null $product */
                $product = Product::query()->lockForUpdate()->find($line['product_id']);

                if (! $product || ! $product->isActive()) {
                    throw new CustomWebException(__('A selected product is no longer available.'), 422);
                }

                $variant = null;

                if (! empty($line['variant_id'])) {
                    $variant = ProductVariant::query()
                        ->lockForUpdate()
                        ->where('product_id', $product->id)
                        ->find($line['variant_id']);

                    if (! $variant) {
                        throw new CustomWebException(__('A selected product option is no longer available.'), 422);
                    }

                    if (! $variant->isInStock($quantity)) {
                        throw new CustomWebException(
                            __(':product (:variant) is out of stock.', ['product' => $product->name, 'variant' => $variant->name]),
                            422
                        );
                    }

                    $unitPrice = $variant->price();
                    $variant->decrement('stock', $quantity);
                    $product->decrement('stock', $quantity);
                } else {
                    if (! $product->isInStock($quantity)) {
                        throw new CustomWebException(
                            __(':product is out of stock.', ['product' => $product->name]),
                            422
                        );
                    }

                    $unitPrice = (float) $product->price;
                    $product->decrement('stock', $quantity);
                }

                $lineSubtotal = $unitPrice * $quantity;
                $subtotal += $lineSubtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'variant_name' => $variant?->name,
                    'price' => $unitPrice,
                    'quantity' => $quantity,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $order = Order::create([
                'order_number' => UniqueCodeGenerator::make(Order::class, 'order_number', 6, 'ORD'),
                'source' => 'manual',
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
                'discount' => 0,
                'shipping_cost' => $shippingCost,
                'total' => $subtotal + $shippingCost,
            ]);

            $order->items()->createMany($orderItems);

            return $order;
        });
    }
}
