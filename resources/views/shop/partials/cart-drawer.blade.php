{{-- Slide-in cart drawer (populated via /cart/fragment) --}}
<div data-cart-overlay class="fixed inset-0 bg-black/40 z-50 opacity-0 invisible transition-opacity duration-300"></div>

<aside data-cart-drawer
    class="fixed top-0 right-0 h-full w-[90vw] max-w-md bg-white z-50 shadow-2xl flex flex-col translate-x-full transition-transform duration-300 ease-in-out">
    {{-- Header --}}
    <div class="flex items-center justify-between px-5 h-16 border-b border-[color:var(--color-line)] shrink-0">
        <div class="flex items-center gap-2">
            <span class="relative text-[color:var(--color-brand)]">
                <i class="ph ph-shopping-cart-simple text-2xl"></i>
                <span data-cart-count class="absolute -top-2 -right-2 bg-[color:var(--color-brand)] text-white text-[10px] leading-none w-5 h-5 rounded-full flex items-center justify-center">0</span>
            </span>
            <h3 class="font-semibold text-[color:var(--color-ink)]">{{ __('Shopping Cart') }}</h3>
        </div>
        <button type="button" data-cart-close class="text-2xl text-[color:var(--color-muted)] hover:text-[color:var(--color-ink)]" aria-label="{{ __('Close') }}">
            <i class="ph ph-x"></i>
        </button>
    </div>

    {{-- Body (filled by JS) --}}
    <div data-cart-body class="flex-1 flex flex-col overflow-hidden">
        <div class="flex-1 flex items-center justify-center text-[color:var(--color-muted)]">
            <i class="ph ph-circle-notch text-3xl animate-spin"></i>
        </div>
    </div>
</aside>
