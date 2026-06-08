<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Ecommerce\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private Cart $cart) {}

    /**
     * The cart page.
     */
    public function index()
    {
        $items = $this->cart->items();
        $subtotal = $this->cart->subtotal();

        return view('shop.cart', compact('items', 'subtotal'));
    }

    /**
     * Add a product to the cart. Responds with JSON for AJAX add-to-cart.
     */
    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'nullable|integer|min:1',
        ]);

        if (! $product->is_active || ! $product->isInStock()) {
            return response()->json([
                'success' => false,
                'message' => __('This product is out of stock.'),
            ], 422);
        }

        $this->cart->add($product, (int) $request->input('quantity', 1));

        return response()->json([
            'success' => true,
            'message' => __(':product added to cart.', ['product' => $product->name]),
            'count' => $this->cart->count(),
        ]);
    }

    /**
     * Update the quantity of a cart line.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $this->cart->update($product, (int) $request->input('quantity'));

        return redirect()->route('shop.cart.index')->with('success', __('Cart updated.'));
    }

    /**
     * Remove a line from the cart.
     */
    public function remove(int $product)
    {
        $this->cart->remove($product);

        return redirect()->route('shop.cart.index')->with('success', __('Item removed.'));
    }

    /**
     * Lightweight endpoint for the header cart badge.
     */
    public function count()
    {
        return response()->json(['count' => $this->cart->count()]);
    }
}
