@extends('shop.layouts.app')

@section('title', __('Order') . ' #' . $order->order_number . ' — ' . config('application_info.company_info.name'))

@section('content')
    <div class="shop-container py-8">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <div class="text-xs text-[color:var(--color-muted)] mb-1 flex items-center gap-1">
                    <a href="{{ route('home') }}" class="hover:text-[color:var(--color-brand)]">{{ __('Home') }}</a>
                    <span>»</span>
                    <a href="{{ route('shop.account.orders') }}" class="hover:text-[color:var(--color-brand)]">{{ __('My Orders') }}</a>
                    <span>»</span>
                    <span class="text-ink font-medium">{{ __('Order Details') }}</span>
                </div>
                <h1 class="text-2xl font-bold text-ink">{{ __('Order') }} <span class="text-[color:var(--color-brand)]">#{{ $order->order_number }}</span></h1>
            </div>
            
            <div class="flex items-center gap-3 self-start sm:self-auto">
                @if ($order->isOnlinePayable() && \App\Services\Payment\SslCommerzService::isEnabled())
                    <a href="{{ route('shop.payment.sslcommerz.retry', $order->order_number) }}"
                        class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2.5 px-4 rounded-full transition-all shadow-sm">
                        <i class="ph ph-credit-card text-sm"></i> {{ __('Pay Now') }}
                    </a>
                @endif
                <a href="{{ route('shop.account.orders.invoice', $order->order_number) }}"
                    class="inline-flex items-center gap-2 bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white text-xs font-bold py-2.5 px-4 rounded-full transition-all shadow-sm">
                    <i class="ph ph-file-pdf text-sm"></i> {{ __('Download Invoice') }}
                </a>
                <a href="{{ route('shop.account.orders') }}" 
                    class="inline-flex items-center gap-2 border border-neutral-200/80 hover:bg-neutral-50 text-neutral-700 text-xs font-bold py-2.5 px-4 rounded-full transition-all shadow-sm">
                    <i class="ph ph-arrow-left"></i> {{ __('Back to Orders') }}
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            @include('shop.account.partials.sidebar')

            <div class="lg:col-span-3 space-y-6">
                {{-- Order Summary Metadata Card --}}
                <div class="bg-white border border-neutral-200/60 rounded-3xl p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 text-sm shadow-sm">
                    <div>
                        <div class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-1.5">{{ __('Status') }}</div>
                        <span class="inline-block text-white text-[11px] font-bold px-2.5 py-0.5 rounded capitalize
                            {{ $order->status->value === 'pending' ? 'bg-[#17a2b8]' : '' }}
                            {{ $order->status->value === 'confirmed' ? 'bg-blue-500' : '' }}
                            {{ $order->status->value === 'processing' ? 'bg-indigo-500' : '' }}
                            {{ $order->status->value === 'shipped' ? 'bg-purple-500' : '' }}
                            {{ $order->status->value === 'delivered' ? 'bg-emerald-500' : '' }}
                            {{ $order->status->value === 'cancelled' ? 'bg-rose-500' : '' }}
                        ">
                            {{ $order->status_name }}
                        </span>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-1.5">{{ __('Payment') }}</div>
                        <div class="font-semibold text-ink">{{ __('Cash on Delivery') }}</div>
                        <span class="text-xs text-[color:var(--color-muted)]">({{ $order->payment_status_name }})</span>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-1.5">{{ __('Placed on') }}</div>
                        <div class="font-semibold text-ink">{{ $order->created_at->format('d M, Y') }}</div>
                        <span class="text-xs text-[color:var(--color-muted)]">{{ $order->created_at->format('g:i A') }}</span>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-1.5">{{ __('Shipping Address') }}</div>
                        <div class="font-semibold text-ink line-clamp-2" title="{{ $order->shipping_address }}">
                            {{ $order->shipping_address }}@if ($order->city), {{ $order->city }}@endif {{ $order->zip_code }}
                        </div>
                    </div>
                </div>

                {{-- Order Items Details List --}}
                <div class="bg-white border border-neutral-200/60 rounded-3xl p-6 sm:p-8 shadow-sm">
                    <h3 class="text-lg font-bold text-ink mb-6 pb-4 border-b border-neutral-100 flex items-center gap-2">
                        <i class="ph ph-shopping-bag text-xl text-[color:var(--color-brand)]"></i>
                        {{ __('Items in Order') }}
                    </h3>

                    <div class="divide-y divide-neutral-100">
                        @foreach ($order->items as $item)
                            @php
                                $product = $item->product;
                                $imageUrl = $product 
                                    ? ($product->thumbnail ?: ($product->images->isNotEmpty() ? $product->images->first()->image : null))
                                    : null;
                                $hasLink = $product && $product->slug;
                            @endphp
                            <div class="flex items-start gap-4 py-4 first:pt-0 last:pb-0">
                                {{-- Product Thumbnail --}}
                                @if($hasLink)
                                    <a href="{{ route('shop.product', $product->slug) }}" class="block shrink-0 group">
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" 
                                                class="w-16 h-16 object-cover rounded-xl border border-neutral-100 group-hover:opacity-90 transition-opacity">
                                        @else
                                            <div class="w-16 h-16 bg-neutral-50 rounded-xl border border-neutral-100 flex items-center justify-center text-neutral-300 group-hover:bg-neutral-100 transition-colors">
                                                <i class="ph ph-image text-3xl"></i>
                                            </div>
                                        @endif
                                    </a>
                                @else
                                    <div class="shrink-0">
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" class="w-16 h-16 object-cover rounded-xl border border-neutral-100">
                                        @else
                                            <div class="w-16 h-16 bg-neutral-50 rounded-xl border border-neutral-100 flex items-center justify-center text-neutral-300">
                                                <i class="ph ph-image text-3xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Product Name & Attributes --}}
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-ink truncate hover:text-[color:var(--color-brand)] transition-colors">
                                        @if($hasLink)
                                            <a href="{{ route('shop.product', $product->slug) }}">{{ $item->product_name }}</a>
                                        @else
                                            {{ $item->product_name }}
                                        @endif
                                    </h4>

                                    {{-- Selected Variant Info --}}
                                    @if ($item->variant)
                                        <div class="mt-1 flex items-center gap-1.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-neutral-100 text-neutral-600 uppercase tracking-wider">
                                                {{ $item->variant->name }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="mt-1.5 text-xs text-[color:var(--color-muted)] flex items-center gap-1">
                                        <span>{{ __('Qty:') }}</span>
                                        <span class="font-bold text-neutral-700">{{ $item->quantity }}</span>
                                        <span class="mx-1">×</span>
                                        <span>{{ amountWithSymbol($item->price) }}</span>
                                    </div>
                                </div>

                                {{-- Item Total --}}
                                <div class="text-sm font-bold text-ink whitespace-nowrap text-right">
                                    {{ amountWithSymbol($item->subtotal) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Receipt Totals Summary --}}
                    <div class="border-t border-neutral-100 mt-6 pt-6 space-y-3 text-sm">
                        <div class="flex justify-between items-center text-neutral-600">
                            <span>{{ __('Subtotal') }}</span>
                            <span class="font-semibold">{{ amountWithSymbol($order->subtotal) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-neutral-600">
                            <span>{{ __('Shipping Cost') }}</span>
                            <span class="font-semibold">
                                {{ $order->shipping_cost > 0 ? amountWithSymbol($order->shipping_cost) : __('Free') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center font-bold text-ink text-lg pt-2 border-t border-neutral-50">
                            <span>{{ __('Grand Total') }}</span>
                            <span class="text-[color:var(--color-brand)]">{{ amountWithSymbol($order->total) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
