@extends('shop.layouts.app')

@section('title', __('Order Confirmed') . ' — ' . config('application_info.company_info.name'))

@section('content')
    <div class="max-w-3xl mx-auto px-4 py-12">
        <div class="bg-white border border-neutral-100 rounded p-8 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-emerald-100 flex items-center justify-center">
                <i class="ph ph-check-circle text-4xl text-emerald-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-ink mt-4">{{ __('Thank you for your order!') }}</h1>
            <p class="text-[color:var(--color-muted)] mt-2">
                {{ __('Your order has been placed successfully. We will contact you shortly to confirm delivery.') }}
            </p>
            <div class="mt-4 inline-block bg-[color:var(--color-sand)] border border-neutral-100 rounded px-4 py-2">
                <span class="text-sm text-[color:var(--color-muted)]">{{ __('Order Number') }}:</span>
                <span class="font-bold text-[color:var(--color-brand)]">{{ $order->order_number }}</span>
            </div>
        </div>

        <div class="bg-white border border-neutral-100 rounded p-6 mt-6">
            <h3 class="font-semibold text-ink mb-4">{{ __('Order Details') }}</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-5">
                <div>
                    <div class="text-[color:var(--color-muted)]">{{ __('Customer') }}</div>
                    <div class="font-medium">{{ $order->customer_name }}</div>
                    <div>{{ $order->customer_phone }}</div>
                    @if ($order->customer_email)<div>{{ $order->customer_email }}</div>@endif
                </div>
                <div>
                    <div class="text-[color:var(--color-muted)]">{{ __('Shipping Address') }}</div>
                    <div>{{ $order->shipping_address }}</div>
                    @if ($order->city)<div>{{ $order->city }} {{ $order->zip_code }}</div>@endif
                </div>
                <div>
                    <div class="text-[color:var(--color-muted)]">{{ __('Payment Method') }}</div>
                    <div class="font-medium">{{ __('Cash on Delivery') }}</div>
                </div>
                <div>
                    <div class="text-[color:var(--color-muted)]">{{ __('Status') }}</div>
                    <div class="font-medium capitalize">{{ $order->status_name }}</div>
                </div>
            </div>

            <div class="border-t border-neutral-100 pt-4 space-y-2">
                @foreach ($order->items as $item)
                    <div class="flex justify-between text-sm">
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

        <div class="text-center mt-6">
            <a href="{{ route('shop.index') }}"
                class="inline-block bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white font-medium px-6 py-2.5 rounded transition">
                {{ __('Continue Shopping') }}
            </a>
        </div>
    </div>
@endsection
