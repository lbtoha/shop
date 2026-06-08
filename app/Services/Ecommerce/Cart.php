<?php

namespace App\Services\Ecommerce;

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

    public function count(): int
    {
        return (int) $this->items()->sum('quantity');
    }
}
