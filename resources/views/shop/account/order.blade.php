@extends('shop.layouts.app')

@section('title', $order->order_number . ' — ' . config('application_info.company_info.name'))

@section('content')
    <div class="shop-container py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-ink">{{ __('Order') }} {{ $order->order_number }}</h1>
            <a href="{{ route('shop.account.orders') }}" class="text-sm text-[color:var(--color-brand)] hover:underline">&larr; {{ __('Back to orders') }}</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            @include('shop.account.partials.sidebar')

            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white border border-neutral-100 rounded p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-[color:var(--color-muted)]">{{ __('Status') }}</div>
                        <div class="font-medium capitalize">{{ $order->status_name }}</div>
                    </div>
                    <div>
                        <div class="text-[color:var(--color-muted)]">{{ __('Payment') }}</div>
                        <div class="font-medium">{{ __('Cash on Delivery') }} ({{ $order->payment_status_name }})</div>
                    </div>
                    <div>
                        <div class="text-[color:var(--color-muted)]">{{ __('Placed on') }}</div>
                        <div class="font-medium">{{ $order->created_at->format('M d, Y g:i A') }}</div>
                    </div>
                    <div>
                        <div class="text-[color:var(--color-muted)]">{{ __('Shipping Address') }}</div>
                        <div>{{ $order->shipping_address }}@if ($order->city), {{ $order->city }}@endif {{ $order->zip_code }}</div>
                    </div>
                </div>

                <div class="bg-white border border-neutral-100 rounded p-6">
                    <h3 class="font-semibold text-ink mb-4">{{ __('Items') }}</h3>
                    <div class="divide-y divide-neutral-100">
                        @foreach ($order->items as $item)
                            <div class="flex justify-between py-2 text-sm">
                                <span>{{ $item->product_name }} <span class="text-[color:var(--color-muted)]">× {{ $item->quantity }}</span></span>
                                <span class="font-medium">{{ amountWithSymbol($item->subtotal) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-neutral-100 mt-4 pt-4 space-y-1 text-sm">
                        <div class="flex justify-between"><span class="text-[color:var(--color-muted)]">{{ __('Subtotal') }}</span><span>{{ amountWithSymbol($order->subtotal) }}</span></div>
                        <div class="flex justify-between"><span class="text-[color:var(--color-muted)]">{{ __('Shipping') }}</span><span>{{ $order->shipping_cost > 0 ? amountWithSymbol($order->shipping_cost) : __('Free') }}</span></div>
                        <div class="flex justify-between font-bold text-ink text-base mt-1"><span>{{ __('Total') }}</span><span>{{ amountWithSymbol($order->total) }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
