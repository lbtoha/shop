<x-admin-app-layout>
    <div class="grid grid-cols-2 gap-4 xxl:gap-6">

        <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 xxl:grid-cols-4 gap-4 xxl:gap-6">
            @foreach ($state as $item)
                <div class="white-box">
                    <div class="flex justify-between items-center gap-3 mb-1 xxl:mb-5">
                        <div>
                            <p class="s-text mb-1">{{ $item['title'] }}</p>
                            <p class="l-text font-semibold">{{ $item['data'] }}</p>
                        </div>
                        <div class="size-11 rounded-full bg-primary f-center">
                            <i class="{{ $item['icon'] }} text-xl text-white"></i>
                        </div>
                    </div>
                    <a href="{{ $item['url'] }}" class="text-blue font-medium text-xs underline">{{ __('View all') }}</a>
                </div>
            @endforeach
        </div>

        {{-- E-commerce summary cards --}}
        <div class="col-span-2 grid grid-cols-2 xl:grid-cols-4 gap-4 xxl:gap-6">
            @php
                $ecards = [
                    ['title' => __('Total Orders'), 'data' => $orderStats['total'], 'icon' => 'ph ph-shopping-bag', 'url' => route('admin.orders.index')],
                    ['title' => __('Pending Orders'), 'data' => $orderStats['pending'], 'icon' => 'ph ph-clock', 'url' => route('admin.orders.index', ['status' => 'pending'])],
                    ['title' => __('Revenue'), 'data' => amountWithSymbol($orderStats['revenue']), 'icon' => 'ph ph-currency-circle-dollar', 'url' => route('admin.orders.index')],
                    ['title' => __('Products'), 'data' => $orderStats['products'], 'icon' => 'ph ph-package', 'url' => route('admin.products.index')],
                ];
            @endphp
            @foreach ($ecards as $item)
                <div class="white-box">
                    <div class="flex justify-between items-center gap-3 mb-1 xxl:mb-5">
                        <div>
                            <p class="s-text mb-1">{{ $item['title'] }}</p>
                            <p class="l-text font-semibold">{{ $item['data'] }}</p>
                        </div>
                        <div class="size-11 rounded-full bg-primary f-center">
                            <i class="{{ $item['icon'] }} text-xl text-white"></i>
                        </div>
                    </div>
                    <a href="{{ $item['url'] }}" class="text-blue font-medium text-xs underline">{{ __('View all') }}</a>
                </div>
            @endforeach
        </div>

        {{-- Recent orders --}}
        <div class="col-span-2 lg:col-span-1 white-box">
            <div class="flex justify-between items-center mb-4">
                <p class="m-text font-medium">{{ __('Recent Orders') }}</p>
                <a href="{{ route('admin.orders.index') }}" class="text-blue text-xs underline">{{ __('View all') }}</a>
            </div>
            @if ($recentOrders->isEmpty())
                <p class="s-text text-center py-6">{{ __('No orders yet.') }}</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-neutral-30 dark:border-neutral-500">
                                <th class="py-2 pe-2 s-text font-medium">{{ __('Order #') }}</th>
                                <th class="py-2 px-2 s-text font-medium">{{ __('Customer') }}</th>
                                <th class="py-2 px-2 s-text font-medium text-end">{{ __('Total') }}</th>
                                <th class="py-2 ps-2 s-text font-medium text-end">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentOrders as $order)
                                <tr class="border-b border-neutral-20 dark:border-neutral-600">
                                    <td class="py-2 pe-2">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="s-text text-primary font-medium">#{{ $order->order_number }}</a>
                                    </td>
                                    <td class="py-2 px-2 s-text">{{ \Illuminate\Support\Str::limit($order->customer_name, 16) }}</td>
                                    <td class="py-2 px-2 s-text text-end">{{ amountWithSymbol($order->total) }}</td>
                                    <td class="py-2 ps-2 text-end"><span class="status {{ $order->status->color() }} capitalize">{{ __($order->status->label()) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Orders by status + low stock --}}
        <div class="col-span-2 lg:col-span-1 white-box">
            <p class="m-text font-medium mb-4">{{ __('Orders by Status') }}</p>
            <div class="space-y-2 mb-6">
                @foreach ($ordersByStatus as $row)
                    <div class="flex items-center justify-between s-text">
                        <span class="status {{ $row['color'] }} capitalize">{{ $row['label'] }}</span>
                        <span class="font-medium">{{ $row['count'] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-between items-center mb-3">
                <p class="m-text font-medium">{{ __('Low Stock') }}</p>
                <a href="{{ route('admin.products.index') }}" class="text-blue text-xs underline">{{ __('View all') }}</a>
            </div>
            @if ($lowStock->isEmpty())
                <p class="s-text">{{ __('All products well stocked.') }}</p>
            @else
                <div class="space-y-2">
                    @foreach ($lowStock as $product)
                        <div class="flex items-center justify-between s-text">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="truncate text-primary">{{ \Illuminate\Support\Str::limit($product->name, 22) }}</a>
                            <span class="status {{ $product->stock == 0 ? 'danger' : 'warning' }}">{{ $product->stock }} {{ __('left') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="col-span-2 white-box">
            <div class="flex justify-between items-center gap-4 flex-wrap mb-4 xl:mb-6">
                <p class="m-text font-medium">{{ __('Daily Login Overview (Last 15 days)') }}</p>
            </div>
            <div id="dailyLoginChart"></div>
        </div>
        <div class="col-span-2 lg:col-span-1 white-box">
            <div class="white-box">
                <div class="flex justify-between items-center mb-5 flex-wrap gap-2">
                    <p class="m-text font-medium">{{ __('Login By OS') }}</p>
                    <a href="{{ route('admin.dashboard') }}"
                        class="text-xs text-primary underline font-medium">{{ __('View all') }}</a>
                </div>
                <div class="flex justify-center">
                    <div id="osChartRef"></div>
                </div>
            </div>
        </div>
        <div class="col-span-2 lg:col-span-1 white-box">
            <div class="white-box">
                <div class="flex justify-between items-center mb-5 flex-wrap gap-2">
                    <p class="m-text font-medium">{{ __('Login By Browser') }}</p>
                    <a href="{{ route('admin.dashboard') }}"
                        class="text-xs text-primary underline font-medium">{{ __('View all') }}</a>
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
