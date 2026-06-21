<x-admin-app-layout>
    <div class="space-y-4 xxl:space-y-6">

        {{-- ══════════════════════════════════════════════════════════
             Hero greeting + headline KPIs
        ══════════════════════════════════════════════════════════ --}}
        <div class="dash-hero">
            <div class="relative z-[1] flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div>
                    <p class="text-white/70 text-xs font-semibold uppercase tracking-[0.18em] mb-1">
                        {{ __('Dashboard') }}
                    </p>
                    <h1 class="text-xl xxl:text-2xl font-bold">
                        {{ __('Welcome back') }}, {{ trim(auth('admin')->user()->full_name) ?: __('Admin') }} 👋
                    </h1>
                    <p class="text-white/75 text-sm mt-1">
                        {{ __("Here's what's happening with your store today.") }}
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-3 sm:gap-5 shrink-0">
                    <div class="text-center sm:text-left">
                        <p class="text-white/70 text-[11px] font-medium uppercase tracking-wider">{{ __('Revenue') }}</p>
                        <p class="text-lg xxl:text-xl font-bold mt-0.5">{{ amountWithSymbol($orderStats['revenue']) }}</p>
                    </div>
                    <div class="text-center sm:text-left border-x border-white/15 px-3 sm:px-5">
                        <p class="text-white/70 text-[11px] font-medium uppercase tracking-wider">{{ __('Profit') }}</p>
                        <p class="text-lg xxl:text-xl font-bold mt-0.5">{{ amountWithSymbol($orderStats['profit']) }}</p>
                    </div>
                    <div class="text-center sm:text-left">
                        <p class="text-white/70 text-[11px] font-medium uppercase tracking-wider">{{ __('Orders') }}</p>
                        <p class="text-lg xxl:text-xl font-bold mt-0.5">{{ number_format($orderStats['total']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             E-commerce stat cards (bold tinted)
        ══════════════════════════════════════════════════════════ --}}
        @php
            $ecards = [
                ['title' => __('Total Orders'),     'data' => number_format($orderStats['total']),            'icon' => 'ph ph-shopping-bag',            'tint' => 'primary',   'url' => route('admin.orders.index')],
                ['title' => __('Pending Orders'),   'data' => number_format($orderStats['pending']),          'icon' => 'ph ph-clock',                   'tint' => 'warning',   'url' => route('admin.orders.index', ['status' => 'pending'])],
                ['title' => __('Revenue'),          'data' => amountWithSymbol($orderStats['revenue']),       'icon' => 'ph ph-currency-circle-dollar',  'tint' => 'success',   'url' => route('admin.orders.index')],
                ['title' => __('Estimated Profit'), 'data' => amountWithSymbol($orderStats['profit']),        'icon' => 'ph ph-trend-up',                'tint' => 'info',      'url' => route('admin.orders.index')],
                ['title' => __('Products'),         'data' => number_format($orderStats['products']),         'icon' => 'ph ph-package',                 'tint' => 'secondary', 'url' => route('admin.products.index')],
            ];
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 xxl:gap-6">
            @foreach ($ecards as $item)
                <a href="{{ $item['url'] }}" class="stat-card group">
                    <span class="stat-accent bar-{{ $item['tint'] }}"></span>
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="s-text mb-1.5 truncate">{{ $item['title'] }}</p>
                            <p class="text-xl xxl:text-2xl font-bold text-neutral-700 dark:text-neutral-10 truncate">{{ $item['data'] }}</p>
                        </div>
                        <span class="stat-chip tint-{{ $item['tint'] }}">
                            <i class="{{ $item['icon'] }}"></i>
                        </span>
                    </div>
                    <span class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-primary opacity-0 group-hover:opacity-100 transition-opacity">
                        {{ __('View details') }} <i class="ph ph-arrow-right"></i>
                    </span>
                </a>
            @endforeach
        </div>

        {{-- ══════════════════════════════════════════════════════════
             Quick actions
        ══════════════════════════════════════════════════════════ --}}
        <div class="white-box !rounded-2xl">
            <p class="panel-title mb-4"><span class="dot"></span>{{ __('Quick Actions') }}</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 xxl:gap-4">
                @php
                    $actions = [
                        ['label' => __('Add Product'),   'icon' => 'ph ph-plus-circle',      'url' => route('admin.products.create')],
                        ['label' => __('Orders'),        'icon' => 'ph ph-shopping-bag-open','url' => route('admin.orders.index')],
                        ['label' => __('Products'),      'icon' => 'ph ph-package',          'url' => route('admin.products.index')],
                        ['label' => __('Categories'),    'icon' => 'ph ph-squares-four',     'url' => route('admin.categories.index')],
                        ['label' => __('Home Sections'), 'icon' => 'ph ph-layout',           'url' => route('admin.home-sections.index')],
                        ['label' => __('Settings'),      'icon' => 'ph ph-gear-six',         'url' => route('admin.settings.index')],
                    ];
                @endphp
                @foreach ($actions as $a)
                    <a href="{{ $a['url'] }}" class="quick-action">
                        <i class="{{ $a['icon'] }}"></i>
                        <span>{{ $a['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             Recent orders  +  status / low stock
        ══════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 xxl:gap-6">

            {{-- Recent orders (wide) --}}
            <div class="lg:col-span-2 white-box !rounded-2xl">
                <div class="flex justify-between items-center mb-4">
                    <p class="panel-title"><span class="dot"></span>{{ __('Recent Orders') }}</p>
                    <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-primary hover:underline inline-flex items-center gap-1">
                        {{ __('View all') }} <i class="ph ph-arrow-right"></i>
                    </a>
                </div>
                @if ($recentOrders->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <i class="ph ph-shopping-cart text-4xl text-neutral-50 mb-2"></i>
                        <p class="s-text">{{ __('No orders yet.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-neutral-30 dark:border-neutral-500">
                                    <th class="py-2.5 pe-2 s-text font-semibold">{{ __('Order #') }}</th>
                                    <th class="py-2.5 px-2 s-text font-semibold">{{ __('Customer') }}</th>
                                    <th class="py-2.5 px-2 s-text font-semibold text-center">{{ __('Items') }}</th>
                                    <th class="py-2.5 px-2 s-text font-semibold text-end">{{ __('Total') }}</th>
                                    <th class="py-2.5 ps-2 s-text font-semibold text-end">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentOrders as $order)
                                    <tr class="border-b border-neutral-20 dark:border-neutral-600 hover:bg-neutral-10 dark:hover:bg-neutral-903 transition-colors">
                                        <td class="py-2.5 pe-2">
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="s-text text-primary font-semibold">#{{ $order->order_number }}</a>
                                        </td>
                                        <td class="py-2.5 px-2 s-text">{{ \Illuminate\Support\Str::limit($order->customer_name, 18) }}</td>
                                        <td class="py-2.5 px-2 s-text text-center">{{ $order->items_count }}</td>
                                        <td class="py-2.5 px-2 s-text text-end font-semibold">{{ amountWithSymbol($order->total) }}</td>
                                        <td class="py-2.5 ps-2 text-end"><span class="status {{ $order->status->color() }} capitalize !px-3 !py-1">{{ __($order->status->label()) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Status + low stock (narrow) --}}
            <div class="white-box !rounded-2xl">
                <p class="panel-title mb-4"><span class="dot"></span>{{ __('Orders by Status') }}</p>
                <div class="space-y-1 mb-6">
                    @foreach ($ordersByStatus as $row)
                        <div class="dash-row">
                            <span class="status {{ $row['color'] }} capitalize !px-3 !py-1">{{ $row['label'] }}</span>
                            <span class="text-sm font-bold text-neutral-700 dark:text-neutral-30">{{ $row['count'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center mb-3 pt-4 border-t border-dashed border-neutral-30 dark:border-neutral-500">
                    <p class="panel-title"><span class="dot bg-warning"></span>{{ __('Low Stock') }}</p>
                    <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-primary hover:underline">{{ __('View all') }}</a>
                </div>
                @if ($lowStock->isEmpty())
                    <div class="flex items-center gap-2 text-success s-text px-3 py-2">
                        <i class="ph ph-check-circle text-lg"></i>{{ __('All products well stocked.') }}
                    </div>
                @else
                    <div class="space-y-1">
                        @foreach ($lowStock as $product)
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="dash-row">
                                <span class="truncate text-sm text-neutral-600 dark:text-neutral-30 group-hover:text-primary">{{ \Illuminate\Support\Str::limit($product->name, 22) }}</span>
                                <span class="status {{ $product->stock == 0 ? 'danger' : 'warning' }} !px-3 !py-1 shrink-0">{{ $product->stock }} {{ __('left') }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             Analytics charts
        ══════════════════════════════════════════════════════════ --}}
        <div class="white-box !rounded-2xl">
            <p class="panel-title mb-4 xl:mb-6"><span class="dot"></span>{{ __('Daily Login Overview (Last 15 days)') }}</p>
            <div id="dailyLoginChart"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 xxl:gap-6">
            <div class="white-box !rounded-2xl">
                <div class="flex justify-between items-center mb-5 flex-wrap gap-2">
                    <p class="panel-title"><span class="dot"></span>{{ __('Login By OS') }}</p>
                </div>
                <div class="flex justify-center">
                    <div id="osChartRef"></div>
                </div>
            </div>
            <div class="white-box !rounded-2xl">
                <div class="flex justify-between items-center mb-5 flex-wrap gap-2">
                    <p class="panel-title"><span class="dot"></span>{{ __('Login By Browser') }}</p>
                </div>
                <div class="flex justify-center">
                    <div id="browserChartRef"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/admin/js/dashboard/index.js')
    @endpush
</x-admin-app-layout>
