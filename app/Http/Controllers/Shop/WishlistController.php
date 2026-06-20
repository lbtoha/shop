<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Ecommerce\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(private Wishlist $wishlist) {}

    /**
     * Display the wishlist page.
     */
    public function index()
    {
        $items = $this->wishlist->items();

        return view('shop.wishlist', compact('items'));
    }

    /**
     * Toggle a product in/out of the wishlist.
     */
    public function toggle(Product $product)
    {
        if (! $product->isActive()) {
            return response()->json([
                'success' => false,
                'message' => __('Product is unavailable.')
            ], 422);
        }

        $added = $this->wishlist->toggle($product->id);

        $message = $added
            ? __(':product added to wishlist.', ['product' => $product->name])
            : __(':product removed from wishlist.', ['product' => $product->name]);

        return response()->json([
            'success' => true,
            'added' => $added,
            'count' => $this->wishlist->count(),
            'message' => $message,
        ]);
    }

    /**
     * Get the count of wishlist items.
     */
    public function count()
    {
        return response()->json([
            'count' => $this->wishlist->count()
        ]);
    }
}
