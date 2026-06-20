<?php

namespace App\Services\Ecommerce;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Session-backed wishlist.
 * Stores product IDs in the session.
 */
class Wishlist
{
    private const SESSION_KEY = 'wishlist';

    /**
     * Get the raw list of product IDs in the wishlist.
     *
     * @return array<int>
     */
    public function ids(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    private function persist(array $ids): void
    {
        session()->put(self::SESSION_KEY, array_values(array_unique($ids)));
    }

    /**
     * Add a product ID to the wishlist.
     */
    public function add(int $productId): void
    {
        $ids = $this->ids();
        if (! in_array($productId, $ids, true)) {
            $ids[] = $productId;
            $this->persist($ids);
        }
    }

    /**
     * Remove a product ID from the wishlist.
     */
    public function remove(int $productId): void
    {
        $ids = $this->ids();
        $ids = array_filter($ids, fn($id) => $id !== $productId);
        $this->persist($ids);
    }

    /**
     * Toggle a product ID in the wishlist.
     * Returns true if added, false if removed.
     */
    public function toggle(int $productId): bool
    {
        if ($this->has($productId)) {
            $this->remove($productId);
            return false;
        }

        $this->add($productId);
        return true;
    }

    /**
     * Check if a product ID is in the wishlist.
     */
    public function has(int $productId): bool
    {
        return in_array($productId, $this->ids(), true);
    }

    /**
     * Get the count of items in the wishlist.
     */
    public function count(): int
    {
        return count($this->ids());
    }

    /**
     * Hydrate wishlist product IDs into a collection of active Product models.
     *
     * @return Collection<int, Product>
     */
    public function items(): Collection
    {
        $ids = $this->ids();

        if (empty($ids)) {
            return collect();
        }

        return Product::active()->whereIn('id', $ids)->get();
    }

    /**
     * Clear the wishlist.
     */
    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }
}
