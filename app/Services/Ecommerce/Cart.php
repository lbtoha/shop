<?php

namespace App\Services\Ecommerce;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

/**
 * Session-backed shopping cart.
 *
 * Lines are keyed by "{product_id}" or "{product_id}:{variant_id}" so the same
 * product can sit in the cart once per chosen variant:
 *   [lineKey => ['product_id' => int, 'variant_id' => ?int, 'quantity' => int]]
 * Product/variant details (name/price/stock) are always read fresh from the DB
 * so prices and availability can never go stale in the session.
 */
class Cart
{
    private const SESSION_KEY = 'cart';

    private const COUPON_KEY = 'cart_coupon';

    /**
     * Raw cart lines from the session, keyed by line key.
     */
    private function lines(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    private function persist(array $lines): void
    {
        session()->put(self::SESSION_KEY, $lines);
    }

    /**
     * The session key for a product + optional variant.
     */
    public static function lineKey(int $productId, ?int $variantId = null): string
    {
        return $variantId ? "{$productId}:{$variantId}" : (string) $productId;
    }

    public function add(Product $product, int $quantity = 1, ?int $variantId = null): void
    {
        $lines = $this->lines();
        $key = self::lineKey($product->id, $variantId);
        $current = $lines[$key]['quantity'] ?? 0;

        $lines[$key] = [
            'product_id' => $product->id,
            'variant_id' => $variantId,
            'quantity' => max(1, $current + $quantity),
        ];

        $this->persist($lines);
    }

    public function update(string $lineKey, int $quantity): void
    {
        $lines = $this->lines();

        if (! isset($lines[$lineKey])) {
            return;
        }

        if ($quantity <= 0) {
            unset($lines[$lineKey]);
        } else {
            $lines[$lineKey]['quantity'] = $quantity;
        }

        $this->persist($lines);
    }

    public function remove(string $lineKey): void
    {
        $lines = $this->lines();
        unset($lines[$lineKey]);
        $this->persist($lines);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
        session()->forget(self::COUPON_KEY);
    }

    public function isEmpty(): bool
    {
        return empty($this->lines());
    }

    /**
     * Hydrate cart lines into a collection of items with live product/variant data.
     *
     * @return Collection<int, array{key: string, product: Product, variant: ?ProductVariant, quantity: int, unit_price: float, subtotal: float}>
     */
    public function items(): Collection
    {
        $lines = $this->lines();

        if (empty($lines)) {
            return collect();
        }

        $productIds = collect($lines)->pluck('product_id')->unique()->all();
        $variantIds = collect($lines)->pluck('variant_id')->filter()->unique()->all();

        $products = Product::active()->whereIn('id', $productIds)->get()->keyBy('id');
        $variants = $variantIds
            ? ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id')
            : collect();

        return collect($lines)
            ->map(function ($line, $key) use ($products, $variants) {
                $product = $products->get($line['product_id']);

                if (! $product) {
                    return null;
                }

                $variant = $line['variant_id'] ? $variants->get($line['variant_id']) : null;

                // A line whose variant vanished (deleted) is dropped.
                if ($line['variant_id'] && ! $variant) {
                    return null;
                }

                $available = $variant ? (int) $variant->stock : (int) $product->stock;
                $quantity = min((int) $line['quantity'], max($available, 0));

                if ($quantity <= 0) {
                    return null;
                }

                $unitPrice = $variant ? $variant->price() : (float) $product->price;

                return [
                    'key' => (string) $key,
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice * $quantity,
                ];
            })
            ->filter()
            ->values();
    }

    public function subtotal(): float
    {
        return (float) $this->items()->sum('subtotal');
    }

    /**
     * Total saved versus each product's compare-at (original) price.
     */
    public function discount(): float
    {
        return (float) $this->items()->sum(function ($line) {
            $product = $line['product'];
            if ($product->compare_at_price && $product->compare_at_price > $product->price) {
                return ((float) $product->compare_at_price - (float) $product->price) * $line['quantity'];
            }

            return 0;
        });
    }

    public function count(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    /* ----------------------------- Coupons ----------------------------- */

    /**
     * Store the applied coupon code in the session.
     */
    public function applyCoupon(string $code): void
    {
        session()->put(self::COUPON_KEY, trim($code));
    }

    public function removeCoupon(): void
    {
        session()->forget(self::COUPON_KEY);
    }

    public function couponCode(): ?string
    {
        return session()->get(self::COUPON_KEY);
    }

    /**
     * The applied coupon, only if it still resolves to a real coupon. Does not
     * check redeemability against the current subtotal — use couponDiscount()
     * for the effective discount.
     */
    public function coupon(): ?Coupon
    {
        $code = $this->couponCode();

        return $code ? Coupon::findByCode($code) : null;
    }

    /**
     * Effective coupon discount for the current cart subtotal. Returns 0 (and
     * forgets a now-invalid coupon) when the coupon no longer applies.
     */
    public function couponDiscount(): float
    {
        $coupon = $this->coupon();

        if (! $coupon) {
            return 0;
        }

        $discount = $coupon->discountFor($this->subtotal());

        if ($discount <= 0) {
            // The coupon stopped being valid (expired, subtotal dropped, etc.) —
            // drop it so stale codes don't linger in the session.
            $this->removeCoupon();

            return 0;
        }

        return $discount;
    }

    /**
     * Order total after shipping and coupon discount.
     */
    public function total(float $shippingCost = 0): float
    {
        return max(0, $this->subtotal() - $this->couponDiscount()) + $shippingCost;
    }
}
