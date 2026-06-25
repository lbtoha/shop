@php
    $cartCount = app(\App\Services\Ecommerce\Cart::class)->count();
@endphp

<button id="floating-cart" data-cart-open
    class="fixed right-4 bottom-[144px] lg:bottom-22 z-50 w-14 h-14 rounded-full bg-brand hover:bg-brand-dark text-white flex flex-col items-center justify-center shadow-2xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-105"
    aria-label="{{ __('My Cart') }}">
    <div class="relative">
        <i class="ph-bold ph-shopping-cart-simple text-2xl"></i>
        <span data-cart-count
            class="{{ $cartCount ? '' : 'hidden' }} absolute -top-2.5 -right-2.5 bg-accent text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center shadow-md">
            {{ $cartCount }}
        </span>
    </div>
</button>
