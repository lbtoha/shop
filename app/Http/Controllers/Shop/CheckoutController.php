<?php

namespace App\Http\Controllers\Shop;

use App\Exceptions\CustomWebException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Ecommerce\Cart;
use App\Services\Ecommerce\CheckoutService;
use App\Services\Ecommerce\OrderNotifier;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(private Cart $cart) {}

    /**
     * The checkout form (guest cash-on-delivery).
     */
    public function index()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('shop.cart.index')->with('error', __('Your cart is empty.'));
        }

        $items = $this->cart->items();
        $subtotal = $this->cart->subtotal();
        $shippingCost = (float) getOption('shipping_cost', 0);
        $couponCode = $this->cart->couponCode();
        $couponDiscount = $this->cart->couponDiscount();

        return view('shop.checkout', compact('items', 'subtotal', 'shippingCost', 'couponCode', 'couponDiscount'));
    }

    /**
     * Place a cash-on-delivery order.
     */
    public function store(Request $request, CheckoutService $checkout)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:30',
            'customer_email' => 'nullable|email|max:255',
            'shipping_address' => 'required|string|max:1000',
            'city' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:30',
            'note' => 'nullable|string|max:1000',
        ]);

        $shippingCost = (float) getOption('shipping_cost', 0);

        try {
            $order = $checkout->placeOrder($validated, auth()->id(), $shippingCost);
        } catch (CustomWebException $e) {
            return redirect()->route('shop.cart.index')->with('error', $e->getMessage());
        }

        OrderNotifier::orderPlaced($order);

        // Store the just-placed order number in the session so the confirmation
        // page can verify the viewer is the actual buyer (not a URL guesser).
        session()->put('confirmed_order', $order->order_number);

        return redirect()->route('shop.checkout.confirmation', $order->order_number);
    }

    /**
     * Order confirmation / thank-you page.
     *
     * Only the session that placed the order can view the confirmation page.
     * After first view the session key is cleared so it's a one-time pass.
     */
    public function confirmation(string $orderNumber)
    {
        $order = Order::with(['items.product', 'items.variant'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Security: ensure only the session that just placed this order can see it.
        $confirmedInSession = session()->pull('confirmed_order');
        if ($confirmedInSession !== $orderNumber) {
            // Allow admins or the order's registered user to view it without the session key.
            $isOwner = auth()->check() && auth()->id() === $order->user_id;
            if (! $isOwner) {
                abort(403, __('You do not have permission to view this order confirmation.'));
            }
        }

        return view('shop.confirmation', compact('order'));
    }

    /**
     * Download invoice as PDF.
     */
    public function invoice(string $orderNumber)
    {
        $order = Order::with(['items.product', 'items.variant'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('shop.invoice', compact('order'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }
}
