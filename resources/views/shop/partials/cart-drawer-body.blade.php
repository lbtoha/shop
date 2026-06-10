{{-- Inner content of the slide-in cart drawer. Re-rendered on every cart change. --}}
@if ($items->isEmpty())
    <div class="flex-1 flex flex-col items-center justify-center text-center px-6 py-16">
        <div class="w-24 h-24 rounded-2xl bg-[color:var(--color-image)] flex items-center justify-center mb-5">
            <i class="ph ph-shopping-bag text-4xl text-neutral-400"></i>
        </div>
        <h4 class="text-lg font-semibold text-[color:var(--color-ink)]">{{ __('Your cart is empty') }}</h4>
        <p class="text-sm text-[color:var(--color-muted)] mt-1">{{ __('Add some items to get started!') }}</p>
        <a href="{{ route('shop.index') }}" data-cart-close
            class="mt-6 inline-flex items-center gap-2 bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white text-sm font-medium px-5 py-2.5 rounded-full">
            {{ __('Start Shopping') }} <i class="ph ph-arrow-right"></i>
        </a>
    </div>
@else
    {{-- Items (scrollable) --}}
    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3">
        @foreach ($items as $line)
            @php($product = $line['product'])
            <div class="flex gap-3 border border-[color:var(--color-line)] rounded-2xl p-3">
                <a href="{{ route('shop.product', $product->slug) }}" class="w-16 h-16 shrink-0 rounded-xl bg-[color:var(--color-image)] overflow-hidden flex items-center justify-center">
                    @if ($product->thumbnail)
                        <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <i class="ph ph-image text-xl text-neutral-300"></i>
                    @endif
                </a>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('shop.product', $product->slug) }}" class="block text-sm font-medium text-[color:var(--color-ink)] truncate hover:text-[color:var(--color-brand)]">{{ $product->name }}</a>
                    @if ($line['variant'])
                        <div class="text-xs text-[color:var(--color-brand)] mt-0.5">{{ $line['variant']->name }}</div>
                    @endif
                    <div class="text-xs text-[color:var(--color-muted)] mt-0.5">{{ amountWithSymbol($line['unit_price']) }} {{ __('each') }}</div>
                    <div class="flex items-center justify-between mt-2">
                        {{-- Qty stepper (AJAX) --}}
                        <div class="flex items-center border border-[color:var(--color-line)] rounded-full overflow-hidden text-sm">
                            <button type="button" data-cart-qty="{{ route('shop.cart.update', $line['key']) }}" data-delta="-1" class="px-2.5 py-1 hover:bg-neutral-50">−</button>
                            <span class="px-2 min-w-[1.5rem] text-center" data-qty>{{ $line['quantity'] }}</span>
                            <button type="button" data-cart-qty="{{ route('shop.cart.update', $line['key']) }}" data-delta="1" class="px-2.5 py-1 hover:bg-neutral-50">+</button>
                        </div>
                        <button type="button" data-cart-remove="{{ route('shop.cart.remove', $line['key']) }}" class="text-neutral-400 hover:text-red-500" aria-label="{{ __('Remove') }}">
                            <i class="ph ph-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="text-sm font-semibold text-[color:var(--color-ink)] shrink-0">{{ amountWithSymbol($line['subtotal']) }}</div>
            </div>
        @endforeach
    </div>

    {{-- Totals + actions (sticky footer) --}}
    <div class="border-t border-[color:var(--color-line)] px-5 py-4">
        <div class="flex justify-between text-sm mb-2">
            <span class="text-[color:var(--color-muted)]">{{ __('Subtotal') }}</span>
            <span class="font-semibold text-[color:var(--color-ink)]">{{ amountWithSymbol($subtotal) }}</span>
        </div>
        @if ($discount > 0)
            <div class="flex justify-between text-sm mb-2">
                <span class="text-[color:var(--color-muted)]">{{ __('Discount') }}</span>
                <span class="font-semibold text-[color:var(--color-brand)]">-{{ amountWithSymbol($discount) }}</span>
            </div>
        @endif
        <div class="flex justify-between text-base font-bold text-[color:var(--color-ink)] pt-2 border-t border-[color:var(--color-line)]">
            <span>{{ __('Total') }}</span>
            <span>{{ amountWithSymbol($subtotal) }}</span>
        </div>
        <p class="text-xs text-[color:var(--color-muted)] mt-2">{{ __('Shipping calculated at checkout') }}</p>

        <a href="{{ route('shop.checkout.index') }}" class="mt-4 flex items-center justify-center gap-2 bg-[color:var(--color-ink)] hover:bg-black text-white font-medium py-3 rounded-full">
            {{ __('Checkout') }}
        </a>
        <a href="{{ route('shop.index') }}" data-cart-close class="mt-2 flex items-center justify-center bg-[color:var(--color-brand-soft)] hover:bg-[color:var(--color-brand-light)] text-[color:var(--color-brand-dark)] font-medium py-3 rounded-full">
            {{ __('Continue Shopping') }}
        </a>
        <button type="button" data-cart-clear="{{ route('shop.cart.clear') }}" class="mt-2 w-full text-center text-sm text-red-500 hover:text-red-600 font-medium py-1.5">
            {{ __('Clear all items') }}
        </button>
    </div>
@endif
