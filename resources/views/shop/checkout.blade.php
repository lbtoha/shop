@extends('shop.layouts.app')

@section('title', __('Checkout') . ' — ' . config('application_info.company_info.name'))

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-ink mb-6">{{ __('Checkout') }}</h1>

        <form method="POST" action="{{ route('shop.checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf

            {{-- Shipping / customer details --}}
            <div class="lg:col-span-2 bg-white border border-neutral-100 rounded p-6">
                <h3 class="font-semibold text-ink mb-4">{{ __('Shipping Details') }}</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium mb-1">{{ __('Full Name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                            class="w-full border border-neutral-200 rounded py-2 px-3 focus:outline-none focus:border-[color:var(--color-brand)]">
                        @error('customer_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium mb-1">{{ __('Phone') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" required
                            class="w-full border border-neutral-200 rounded py-2 px-3 focus:outline-none focus:border-[color:var(--color-brand)]">
                        @error('customer_phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1">{{ __('Email') }} <span class="text-neutral-400">({{ __('optional') }})</span></label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                            class="w-full border border-neutral-200 rounded py-2 px-3 focus:outline-none focus:border-[color:var(--color-brand)]">
                        @error('customer_email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1">{{ __('Shipping Address') }} <span class="text-red-500">*</span></label>
                        <textarea name="shipping_address" rows="3" required
                            class="w-full border border-neutral-200 rounded py-2 px-3 focus:outline-none focus:border-[color:var(--color-brand)]">{{ old('shipping_address') }}</textarea>
                        @error('shipping_address')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium mb-1">{{ __('City') }}</label>
                        <input type="text" name="city" value="{{ old('city') }}"
                            class="w-full border border-neutral-200 rounded py-2 px-3 focus:outline-none focus:border-[color:var(--color-brand)]">
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium mb-1">{{ __('Zip Code') }}</label>
                        <input type="text" name="zip_code" value="{{ old('zip_code') }}"
                            class="w-full border border-neutral-200 rounded py-2 px-3 focus:outline-none focus:border-[color:var(--color-brand)]">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1">{{ __('Order Note') }} <span class="text-neutral-400">({{ __('optional') }})</span></label>
                        <textarea name="note" rows="2"
                            class="w-full border border-neutral-200 rounded py-2 px-3 focus:outline-none focus:border-[color:var(--color-brand)]">{{ old('note') }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3 bg-[color:var(--color-sand)] border border-neutral-100 rounded p-4">
                    <input type="radio" checked readonly class="accent-[color:var(--color-brand)]">
                    <div>
                        <div class="font-medium text-ink">{{ __('Cash on Delivery') }}</div>
                        <div class="text-sm text-[color:var(--color-muted)]">{{ __('Pay with cash when your order is delivered.') }}</div>
                    </div>
                    <i class="ph ph-money text-2xl text-[color:var(--color-brand)] ml-auto"></i>
                </div>
            </div>

            {{-- Order summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white border border-neutral-100 rounded p-5 sticky top-24">
                    <h3 class="font-semibold text-ink mb-4">{{ __('Your Order') }}</h3>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @foreach ($items as $line)
                            <div class="flex justify-between text-sm">
                                <span class="text-[color:var(--color-muted)]">{{ $line['product']->name }} <span class="text-xs">× {{ $line['quantity'] }}</span></span>
                                <span class="font-medium shrink-0 ml-2">{{ amountWithSymbol($line['subtotal']) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-neutral-100 my-3"></div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-[color:var(--color-muted)]">{{ __('Subtotal') }}</span>
                        <span>{{ amountWithSymbol($subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-[color:var(--color-muted)]">{{ __('Shipping') }}</span>
                        <span>{{ $shippingCost > 0 ? amountWithSymbol($shippingCost) : __('Free') }}</span>
                    </div>
                    <div class="border-t border-neutral-100 my-3"></div>
                    <div class="flex justify-between font-bold text-ink text-lg">
                        <span>{{ __('Total') }}</span>
                        <span>{{ amountWithSymbol($subtotal + $shippingCost) }}</span>
                    </div>

                    <button type="submit"
                        class="mt-5 w-full bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white font-medium py-3 rounded transition">
                        {{ __('Place Order') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
