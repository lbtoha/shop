@extends('shop.layouts.app')

@section('title', __('Shopping Cart') . ' — ' . config('application_info.company_info.name'))

@section('content')
    <div class="shop-container py-8">
        <h1 class="text-2xl font-bold text-[color:var(--color-ink)] mb-6">{{ __('Shopping Cart') }}</h1>

        @if ($items->isEmpty())
            <div class="bg-white border border-[color:var(--color-line)] rounded-2xl p-14 text-center">
                <i class="ph ph-shopping-cart text-6xl text-neutral-300 block mb-4"></i>
                <p class="text-[color:var(--color-muted)] mb-5">{{ __('Your cart is empty.') }}</p>
                <a href="{{ route('shop.index') }}"
                    class="inline-flex items-center gap-2 bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white font-medium px-6 py-3 rounded-full transition">
                    {{ __('Continue Shopping') }} <i class="ph ph-arrow-right"></i>
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-4">
                    @foreach ($items as $line)
                        @php($product = $line['product'])
                        @php($maxQty = $line['variant'] ? $line['variant']->stock : $product->stock)
                        <div class="bg-white border border-[color:var(--color-line)] rounded-2xl p-4 flex flex-col sm:flex-row items-center gap-4">
                            <a href="{{ route('shop.product', $product->slug) }}"
                                class="w-24 h-24 shrink-0 rounded-xl bg-[color:var(--color-image)] overflow-hidden flex items-center justify-center">
                                @if ($product->thumbnail)
                                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <i class="ph ph-image text-2xl text-neutral-300"></i>
                                @endif
                            </a>

                            <div class="flex-1 w-full">
                                <a href="{{ route('shop.product', $product->slug) }}" class="font-semibold text-[color:var(--color-ink)] hover:text-[color:var(--color-brand)]">{{ $product->name }}</a>
                                @if ($line['variant'])
                                    <div class="text-sm text-[color:var(--color-brand)] mt-0.5">{{ $line['variant']->name }}</div>
                                @endif
                                <div class="text-sm text-[color:var(--color-muted)] mt-1">{{ amountWithSymbol($line['unit_price']) }} {{ __('each') }}</div>

                                <div class="mt-3 flex items-center gap-4 flex-wrap">
                                    <form method="POST" action="{{ route('shop.cart.update', $line['key']) }}" class="flex items-center gap-2" data-qty-wrap>
                                        @csrf @method('PUT')
                                        <div class="flex items-center border border-[color:var(--color-line)] rounded-full overflow-hidden">
                                            <button type="button" data-step="-1" class="px-3 py-1.5 text-lg hover:bg-neutral-50">−</button>
                                            <input type="number" name="quantity" value="{{ $line['quantity'] }}" min="1" max="{{ $maxQty }}"
                                                class="w-12 text-center py-1.5 focus:outline-none">
                                            <button type="button" data-step="1" class="px-3 py-1.5 text-lg hover:bg-neutral-50">+</button>
                                        </div>
                                        <button type="submit" class="text-sm text-[color:var(--color-brand)] hover:underline">{{ __('Update') }}</button>
                                    </form>

                                    <form method="POST" action="{{ route('shop.cart.remove', $line['key']) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm text-red-500 hover:underline flex items-center gap-1">
                                            <i class="ph ph-trash"></i> {{ __('Remove') }}
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="text-right font-bold text-[color:var(--color-ink)] sm:w-24">{{ amountWithSymbol($line['subtotal']) }}</div>
                        </div>
                    @endforeach
                </div>

                {{-- Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-white border border-[color:var(--color-line)] rounded-2xl p-6 sticky top-28">
                        <h3 class="font-bold text-[color:var(--color-ink)] mb-4">{{ __('Order Summary') }}</h3>

                        {{-- Coupon --}}
                        @if ($couponCode && $couponDiscount > 0)
                            <div class="flex items-center justify-between gap-2 bg-[color:var(--color-brand-soft)] border border-[color:var(--color-line)] rounded-xl px-3 py-2.5 mb-4">
                                <div class="flex items-center gap-2 min-w-0">
                                    <i class="ph ph-tag text-[color:var(--color-brand)]"></i>
                                    <span class="text-sm font-medium text-[color:var(--color-ink)] truncate">{{ $couponCode }}</span>
                                </div>
                                <form method="POST" action="{{ route('shop.cart.coupon.remove') }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:underline">{{ __('Remove') }}</button>
                                </form>
                            </div>
                        @else
                            <form method="POST" action="{{ route('shop.cart.coupon.apply') }}" class="flex items-center gap-2 mb-4">
                                @csrf
                                <input type="text" name="code" value="{{ old('code') }}" placeholder="{{ __('Coupon code') }}"
                                    class="flex-1 min-w-0 border border-[color:var(--color-line)] rounded-full py-2 px-4 text-sm focus:outline-none focus:border-[color:var(--color-brand)]">
                                <button type="submit" class="shrink-0 bg-[color:var(--color-ink)] hover:opacity-90 text-white text-sm font-medium px-4 py-2 rounded-full transition">{{ __('Apply') }}</button>
                            </form>
                        @endif

                        <div class="flex justify-between text-sm mb-2.5">
                            <span class="text-[color:var(--color-muted)]">{{ __('Subtotal') }}</span>
                            <span class="font-medium text-[color:var(--color-ink)]">{{ amountWithSymbol($subtotal) }}</span>
                        </div>
                        @if ($couponDiscount > 0)
                            <div class="flex justify-between text-sm mb-2.5 text-[color:var(--color-brand)]">
                                <span>{{ __('Discount') }}</span>
                                <span class="font-medium">−{{ amountWithSymbol($couponDiscount) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-sm mb-2.5">
                            <span class="text-[color:var(--color-muted)]">{{ __('Shipping') }}</span>
                            <span>{{ __('Calculated at checkout') }}</span>
                        </div>
                        <div class="border-t border-[color:var(--color-line)] my-4"></div>
                        <div class="flex justify-between font-bold text-[color:var(--color-ink)] text-lg">
                            <span>{{ __('Total') }}</span>
                            <span>{{ amountWithSymbol(max(0, $subtotal - $couponDiscount)) }}</span>
                        </div>
                        <a href="{{ route('shop.checkout.index') }}"
                            class="mt-5 flex items-center justify-center gap-2 bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white font-medium py-3 rounded-full transition">
                            {{ __('Proceed to Checkout') }} <i class="ph ph-arrow-right"></i>
                        </a>
                        <a href="{{ route('shop.index') }}" class="mt-3 block text-center text-sm text-[color:var(--color-brand)] hover:underline">
                            {{ __('Continue Shopping') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
