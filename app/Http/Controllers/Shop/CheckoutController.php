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

        // Calculate default shipping cost (inside Dhaka) based on items in cart
        $shippingCost = 0.0;
        foreach ($items as $line) {
            $product = $line['product'];
            $cost = (float) ($product->shipping_cost_dhaka ?? 0);
            if ($cost > $shippingCost) {
                $shippingCost = $cost;
            }
        }

        $couponCode = $this->cart->couponCode();
        $couponDiscount = $this->cart->couponDiscount();

        $previousOrder = null;
        if (auth()->check()) {
            $previousOrder = Order::where('user_id', auth()->id())
                ->latest()
                ->first();
        }

        return view('shop.checkout', compact('items', 'subtotal', 'shippingCost', 'couponCode', 'couponDiscount', 'previousOrder'));
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
            'shipping_area' => 'required|string|in:inside,outside',
        ]);

        // Calculate dynamic shipping cost based on selected area and items in cart
        $shippingArea = $validated['shipping_area'];
        $shippingCost = 0.0;
        foreach ($this->cart->items() as $line) {
            $product = $line['product'];
            $cost = $shippingArea === 'inside'
                ? (float) ($product->shipping_cost_dhaka ?? 0)
                : (float) ($product->shipping_cost_outside ?? 0);
            if ($cost > $shippingCost) {
                $shippingCost = $cost;
            }
        }

        $userId = auth()->id();

        if (! auth()->check()) {
            $phone = $validated['customer_phone'];
            $email = $validated['customer_email'] ?? ($phone.'_bd@gmail.com');

            // Find existing user by phone or email
            $user = \App\Models\User::where('phone', $phone)
                ->orWhere('email', $email)
                ->first();

            if (! $user) {
                // Determine a unique username
                $baseUsername = \Illuminate\Support\Str::slug($validated['customer_name'], '_') ?: 'user';
                $username = $baseUsername;
                $i = 1;
                while (\App\Models\User::where('username', $username)->exists()) {
                    $username = $baseUsername.'_'.$i++;
                }

                $user = \App\Models\User::create([
                    'first_name' => $validated['customer_name'],
                    'email' => $email,
                    'phone' => $phone,
                    'username' => $username,
                    'password' => \Illuminate\Support\Facades\Hash::make($phone),
                    'status' => \App\Enums\UserStatusEnum::ACTIVE->value,
                ]);
            }

            // Automatically log them in so their profile is active
            auth()->login($user);
            $request->session()->regenerate();
            $userId = $user->id;
        }

        try {
            $order = $checkout->placeOrder($validated, $userId, $shippingCost);
        } catch (CustomWebException $e) {
            return redirect()->route('shop.cart.index')->with('error', $e->getMessage());
        }

        OrderNotifier::orderPlaced($order);

        // Remember which orders this session placed so the confirmation page and
        // invoice download can verify the viewer is the actual buyer (not a URL
        // guesser) — order numbers are sequential and therefore enumerable.
        $placed = session()->get('placed_orders', []);
        $placed[] = $order->order_number;
        session()->put('placed_orders', array_values(array_unique($placed)));

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

        $this->authorizeOrderView($order);

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

        $this->authorizeOrderView($order);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('shop.invoice', compact('order'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }

    /**
     * Guard guest-facing order pages. Order numbers are sequential and so
     * guessable; only the buyer's session, the order's registered owner, or an
     * authenticated admin may view an order's details / invoice.
     */
    private function authorizeOrderView(Order $order): void
    {
        $placedInSession = in_array($order->order_number, session()->get('placed_orders', []), true);
        $isOwner = auth()->check() && auth()->id() === $order->user_id;
        $isAdmin = auth('admin')->check();

        abort_unless($placedInSession || $isOwner || $isAdmin, 403, __('You do not have permission to view this order.'));
    }
}
