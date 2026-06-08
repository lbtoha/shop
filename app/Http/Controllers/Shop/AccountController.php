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
        $order = Order::with('items')
            ->where('user_id', auth()->id())
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('shop.account.order', compact('order'));
    }
}
