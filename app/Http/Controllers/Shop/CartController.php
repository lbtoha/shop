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
        $couponCode = $this->cart->couponCode();
        $couponDiscount = $this->cart->couponDiscount();

        return view('shop.cart', compact('items', 'subtotal', 'couponCode', 'couponDiscount'));
    }

    /**
     * Apply a coupon code to the cart.
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:64',
        ]);

        if ($this->cart->isEmpty()) {
            return back()->with('error', __('Your cart is empty.'));
        }

        $coupon = \App\Models\Coupon::findByCode($request->input('code'));
        $reason = null;

        if (! $coupon || ! $coupon->isRedeemable($this->cart->subtotal(), $reason)) {
            return back()->with('error', $reason ?: __('Invalid coupon code.'));
        }

        $this->cart->applyCoupon($coupon->code);

        return back()->with('success', __('Coupon ":code" applied — you saved :amount.', [
            'code' => $coupon->code,
            'amount' => amountWithSymbol($coupon->discountFor($this->cart->subtotal())),
        ]));
    }

    /**
     * Remove the applied coupon.
     */
    public function removeCoupon()
    {
        $this->cart->removeCoupon();

        return back()->with('success', __('Coupon removed.'));
    }

    /**
     * Add a product to the cart. Responds with JSON for AJAX add-to-cart.
     */
    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'nullable|integer|min:1',
            'variant_id' => 'nullable|integer',
        ]);

        if (! $product->isActive()) {
            return response()->json(['success' => false, 'message' => __('This product is out of stock.')], 422);
        }

        $variant = null;
        $variantId = $request->filled('variant_id') ? (int) $request->input('variant_id') : null;

        // If the product sells through variants, one must be chosen and in stock.
        if ($product->hasVariants()) {
            if (! $variantId) {
                return response()->json(['success' => false, 'message' => __('Please select an option first.')], 422);
            }

            $variant = $product->variants()->find($variantId);

            if (! $variant) {
                return response()->json(['success' => false, 'message' => __('Selected option is unavailable.')], 422);
            }
        } else {
            $variantId = null; // ignore stray variant_id on simple products
        }

        // Validate stock against total requested quantity (existing in cart + new quantity)
        $quantity = (int) $request->input('quantity', 1);
        $key = \App\Services\Ecommerce\Cart::lineKey($product->id, $variantId);
        $existingLines = session()->get('cart', []);
        $currentInCart = $existingLines[$key]['quantity'] ?? 0;
        $newTotal = $currentInCart + $quantity;

        if ($variant) {
            if (! $variant->isInStock($newTotal)) {
                $available = max(0, $variant->stock - $currentInCart);
                if ($available > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Only :count more of this option is available.', ['count' => $available])
                    ], 422);
                }
                return response()->json(['success' => false, 'message' => __('This option is out of stock.')], 422);
            }
        } else {
            if (! $product->isInStock($newTotal)) {
                $available = max(0, $product->stock - $currentInCart);
                if ($available > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Only :count more of this product is available.', ['count' => $available])
                    ], 422);
                }
                return response()->json(['success' => false, 'message' => __('This product is out of stock.')], 422);
            }
        }

        $this->cart->add($product, $quantity, $variantId);

        $label = $variant ? $product->name.' ('.$variant->name.')' : $product->name;

        return response()->json([
            'success' => true,
            'message' => __(':product added to cart.', ['product' => $label]),
            'count' => $this->cart->count(),
            'drawer' => $this->renderDrawer(),
        ]);
    }

    /**
     * Update the quantity of a cart line. AJAX returns the refreshed drawer.
     */
    public function update(Request $request, string $lineKey)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $quantity = (int) $request->input('quantity');

        if ($quantity > 0) {
            $lines = session()->get('cart', []);
            if (isset($lines[$lineKey])) {
                $line = $lines[$lineKey];
                $product = Product::find($line['product_id']);
                if ($product) {
                    $variant = $line['variant_id'] ? \App\Models\ProductVariant::find($line['variant_id']) : null;
                    $available = $variant ? $variant->stock : $product->stock;
                    if ($quantity > $available) {
                        return response()->json([
                            'success' => false,
                            'message' => __('Only :count items are available in stock.', ['count' => $available])
                        ], 422);
                    }
                }
            }
        }

        $this->cart->update($lineKey, $quantity);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count' => $this->cart->count(),
                'subtotal' => $this->cart->subtotal(),
                'couponDiscount' => $this->cart->couponDiscount(),
                'couponCode' => $this->cart->couponCode(),
                'discount' => $this->cart->discount(),
                'drawer' => $this->renderDrawer(),
            ]);
        }

        return redirect()->route('shop.cart.index')->with('success', __('Cart updated.'));
    }

    /**
     * Remove a line from the cart. AJAX returns the refreshed drawer.
     */
    public function remove(Request $request, string $lineKey)
    {
        $this->cart->remove($lineKey);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count' => $this->cart->count(),
                'subtotal' => $this->cart->subtotal(),
                'couponDiscount' => $this->cart->couponDiscount(),
                'couponCode' => $this->cart->couponCode(),
                'discount' => $this->cart->discount(),
                'drawer' => $this->renderDrawer(),
            ]);
        }

        return redirect()->route('shop.cart.index')->with('success', __('Item removed.'));
    }

    /**
     * Empty the cart entirely (drawer "clear all").
     */
    public function clear(Request $request)
    {
        $this->cart->clear();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count' => 0,
                'drawer' => $this->renderDrawer(),
            ]);
        }

        return redirect()->route('shop.cart.index')->with('success', __('Cart cleared.'));
    }

    /**
     * Return the rendered drawer body (used to populate the slide-in cart).
     */
    public function fragment()
    {
        return response()->json([
            'count' => $this->cart->count(),
            'drawer' => $this->renderDrawer(),
        ]);
    }

    private function renderDrawer(): string
    {
        return view('shop.partials.cart-drawer-body', [
            'items' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
            'discount' => $this->cart->discount(),
        ])->render();
    }

    /**
     * Lightweight endpoint for the header cart badge.
     */
    public function count()
    {
        return response()->json(['count' => $this->cart->count()]);
    }
}
