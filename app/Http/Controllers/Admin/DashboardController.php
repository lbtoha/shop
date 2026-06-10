<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\AdminDashboardOverview\UserLoginLogOverview;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        adminUserHasPermission(permission: 'read');

        $user_query = User::query();

        // E-commerce widgets
        $orderStats = [
            'total'    => Order::count(),
            'pending'  => Order::where('status', OrderStatusEnum::PENDING->value)->count(),
            'revenue'  => (float) Order::where('status', '!=', OrderStatusEnum::CANCELLED->value)->sum('total'),
            'products' => Product::count(),
        ];

        // Eager-load items count to avoid N+1 on the recent orders table.
        $recentOrders = Order::withCount('items')->latest()->take(8)->get();

        $ordersByStatus = collect(OrderStatusEnum::cases())->map(fn ($c) => [
            'label' => __($c->label()),
            'color' => $c->color(),
            'count' => Order::where('status', $c->value)->count(),
        ]);

        // Low-stock: products whose own stock is low AND have no variants,
        // PLUS products that sell exclusively through variants but whose total
        // variant stock is critically low (<=5 total).
        $lowStockSimple = Product::withCount('variants')
            ->having('variants_count', '=', 0)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->take(6)
            ->get();

        $lowStockVariant = Product::withCount('variants')
            ->having('variants_count', '>', 0)
            ->whereHas('variants')
            ->withSum('variants as variant_stock_sum', 'stock')
            ->having('variant_stock_sum', '<=', 5)
            ->orderBy('variant_stock_sum')
            ->take(6)
            ->get()
            ->each(fn ($p) => $p->setAttribute('stock', (int) $p->variant_stock_sum));

        $lowStock = $lowStockSimple->merge($lowStockVariant)->take(6);

        $state = [
            [
                'title' => __('Total Users'),
                'data' => (clone $user_query)->count(),
                'icon' => 'ph ph-users-three',
                'url' => route('admin.users.index'),
            ],
            [
                'title' => __('Active Users'),
                'data' => (clone $user_query)->where('status', 'active')->count(),
                'icon' => 'ph ph-user-check',
                'url' => route('admin.users.index', ['type' => 'active']),
            ],
            [
                'title' => __('Inactive Users'),
                'data' => (clone $user_query)->where('status', 'inactive')->count(),
                'icon' => 'ph ph-user-minus',
                'url' => route('admin.users.index', ['type' => 'inactive']),
            ],
            [
                'title' => __('Banned Users'),
                'data' => (clone $user_query)->where('status', 'banned')->count(),
                'icon' => 'ph ph-user-minus',
                'url' => route('admin.users.index', ['type' => 'banned']),
            ],
        ];

        return view('admin.pages.dashboard', compact('state', 'orderStats', 'recentOrders', 'ordersByStatus', 'lowStock'));
    }

    public function loginLogOverviewBYDay(UserLoginLogOverview $loginLogDaysOverview)
    {
        return response()->json($loginLogDaysOverview->overviewDaysBased());
    }

    public function loginLogOverview(UserLoginLogOverview $loginLogOSOverview)
    {
        return response()->json($loginLogOSOverview->overviewOSBased());
    }

    public function loginLogBrowserOverview(UserLoginLogOverview $loginLogBrowserOverview)
    {
        return response()->json($loginLogBrowserOverview->overviewBrowserBased());
    }
}
