@extends('shop.layouts.app')

@section('title', __('Shopping Cart') . ' — ' . config('application_info.company_info.name'))

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-ink mb-6">{{ __('Shopping Cart') }}</h1>

        @if ($items->isEmpty())
            <div class="bg-white border border-neutral-100 rounded p-12 text-center">
                <i class="ph ph-shopping-cart text-6xl text-neutral-300 block mb-4"></i>
                <p class="text-[color:var(--color-muted)] mb-5">{{ __('Your cart is empty.') }}</p>
                <a href="{{ route('shop.index') }}"
                    class="inline-block bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white font-medium px-6 py-2.5 rounded transition">
                    {{ __('Continue Shopping') }}
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-4">
                    @foreach ($items as $line)
                        @php($product = $line['product'])
                        <div class="bg-white border border-neutral-100 rounded p-4 flex gap-4">
                            <a href="{{ route('shop.product', $product->slug) }}"
                                class="w-20 h-20 shrink-0 bg-neutral-50 overflow-hidden flex items-center justify-center">
                                @if ($product->thumbnail)
                                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <i class="ph ph-image text-2xl text-neutral-300"></i>
                                @endif
                            </a>

                            <div class="flex-1">
                                <a href="{{ route('shop.product', $product->slug) }}" class="font-medium text-ink hover:text-[color:var(--color-brand)]">{{ $product->name }}</a>
                                <div class="text-sm text-[color:var(--color-muted)] mt-1">{{ amountWithSymbol($product->price) }} {{ __('each') }}</div>

                                <div class="mt-3 flex items-center gap-4">
                                    <form method="POST" action="{{ route('shop.cart.update', $product->id) }}" class="flex items-center gap-2">
                                        @csrf @method('PUT')
                                        <input type="number" name="quantity" value="{{ $line['quantity'] }}" min="1" max="{{ $product->stock }}"
                                            class="w-16 border border-neutral-200 rounded py-1 px-2 text-center text-sm">
                                        <button type="submit" class="text-sm text-[color:var(--color-brand)] hover:underline">{{ __('Update') }}</button>
                                    </form>

                                    <form method="POST" action="{{ route('shop.cart.remove', $product->id) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm text-red-500 hover:underline flex items-center gap-1">
                                            <i class="ph ph-trash"></i> {{ __('Remove') }}
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="text-right font-semibold text-ink">{{ amountWithSymbol($line['subtotal']) }}</div>
                        </div>
                    @endforeach
                </div>

                {{-- Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-white border border-neutral-100 rounded p-5 sticky top-24">
                        <h3 class="font-semibold text-ink mb-4">{{ __('Order Summary') }}</h3>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-[color:var(--color-muted)]">{{ __('Subtotal') }}</span>
                            <span class="font-medium">{{ amountWithSymbol($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-[color:var(--color-muted)]">{{ __('Shipping') }}</span>
                            <span>{{ __('Calculated at checkout') }}</span>
                        </div>
                        <div class="border-t border-neutral-100 my-3"></div>
                        <div class="flex justify-between font-bold text-ink">
                            <span>{{ __('Total') }}</span>
                            <span>{{ amountWithSymbol($subtotal) }}</span>
                        </div>
                        <a href="{{ route('shop.checkout.index') }}"
                            class="mt-5 block text-center bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white font-medium py-3 rounded transition">
                            {{ __('Proceed to Checkout') }}
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
