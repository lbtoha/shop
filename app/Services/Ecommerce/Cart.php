<?php

namespace App\Services\Ecommerce;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Session-backed shopping cart.
 *
 * Items are stored keyed by product id as:
 *   ['product_id' => int, 'quantity' => int]
 * Product details (name/price/stock) are always read fresh from the DB so
 * prices and availability can never go stale in the session.
 */
class Cart
{
    private const SESSION_KEY = 'cart';

    private const COUPON_KEY = 'cart_coupon';

    /**
     * Raw cart lines from the session: [product_id => quantity].
     */
    private function lines(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    private function persist(array $lines): void
    {
        session()->put(self::SESSION_KEY, $lines);
    }

    public function add(Product $product, int $quantity = 1): void
    {
        $lines = $this->lines();
        $current = $lines[$product->id] ?? 0;
        $lines[$product->id] = max(1, $current + $quantity);
        $this->persist($lines);
    }

    public function update(Product $product, int $quantity): void
    {
        $lines = $this->lines();

        if ($quantity <= 0) {
            unset($lines[$product->id]);
        } else {
            $lines[$product->id] = $quantity;
        }

        $this->persist($lines);
    }

    public function remove(int $productId): void
    {
        $lines = $this->lines();
        unset($lines[$productId]);
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
     * Hydrate cart lines into a collection of items with live product data.
     *
     * @return Collection<int, array{product: Product, quantity: int, subtotal: float}>
     */
    public function items(): Collection
    {
        $lines = $this->lines();

        if (empty($lines)) {
            return collect();
        }

        $products = Product::active()->whereIn('id', array_keys($lines))->get()->keyBy('id');

        return collect($lines)
            ->map(function ($quantity, $productId) use ($products) {
                $product = $products->get($productId);

                if (! $product) {
                    return null;
                }

                $quantity = min($quantity, max($product->stock, 0));

                if ($quantity <= 0) {
                    return null;
                }

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => (float) $product->price * $quantity,
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
