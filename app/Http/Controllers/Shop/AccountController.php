<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;

class AccountController extends Controller
{
    /**
     * Account dashboard — recent orders and profile summary.
     */
    public function index()
    {
        $user = auth()->user();

        $orders = Order::where('user_id', $user->id)
            ->withCount('items')
            ->latest()
            ->take(5)
            ->get();

        $orderCount = Order::where('user_id', $user->id)->count();

        return view('shop.account.index', compact('user', 'orders', 'orderCount'));
    }

    /**
     * Full order history for the logged-in customer.
     */
    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('shop.account.orders', compact('orders'));
    }

    /**
     * A single order's details (owned by the customer).
     */
    public function showOrder(string $orderNumber)
    {
        $order = Order::with('items.product.images', 'items.variant')
            ->where('user_id', auth()->id())
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('shop.account.order', compact('order'));
    }

    /**
     * Show the profile edit page.
     */
    public function profile()
    {
        $user = auth()->user();
        return view('shop.account.profile', compact('user'));
    }

    /**
     * Update customer profile information.
     */
    public function updateProfile(\Illuminate\Http\Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user->first_name = $validated['first_name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('shop.account.profile')->with('success', __('Profile updated successfully.'));
    }

    /**
     * Cancel a pending order.
     */
    public function cancelOrder(string $orderNumber)
    {
        $order = Order::where('user_id', auth()->id())
            ->where('order_number', $orderNumber)
            ->where('status', \App\Enums\OrderStatusEnum::PENDING->value)
            ->firstOrFail();

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Restock items
            $order->loadMissing('items');
            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    $variant = \App\Models\ProductVariant::find($item->variant_id);
                    if ($variant) {
                        $variant->increment('stock', $item->quantity);
                        $product = \App\Models\Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                        }
                    }
                } else {
                    $product = \App\Models\Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }

            // Restore coupon usage count
            if ($order->coupon_id) {
                $coupon = \App\Models\Coupon::find($order->coupon_id);
                if ($coupon && $coupon->used_count > 0) {
                    $coupon->decrement('used_count');
                }
            }

            // Update order status
            $order->update(['status' => \App\Enums\OrderStatusEnum::CANCELLED->value]);

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', __('Failed to cancel order: ') . $th->getMessage());
        }

        // Send status updated notification
        \App\Services\Ecommerce\OrderNotifier::statusUpdated($order->fresh());

        return back()->with('success', __('Order cancelled successfully.'));
    }

    /**
     * Download customer order invoice.
     */
    public function downloadInvoice(string $orderNumber)
    {
        $order = Order::with(['items.product', 'items.variant'])
            ->where('user_id', auth()->id())
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('shop.invoice', compact('order'));

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }
}
