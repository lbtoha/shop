@php
    $cartCount = app(\App\Services\Ecommerce\Cart::class)->count();
    $wishlistCount = app(\App\Services\Ecommerce\Wishlist::class)->count();
@endphp

{{-- Mobile bottom navigation bar --}}
<div class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-line-soft shadow-[0_-4px_16px_rgba(0,0,0,0.04)] pb-safe">
    <div class="grid grid-cols-5 h-[60px] max-w-md mx-auto items-center text-center">
        
        {{-- Home --}}
        <a href="{{ route('home') }}" 
           class="flex flex-col items-center justify-center gap-0.5 h-full transition-all duration-150 active:scale-95 {{ request()->routeIs('home') ? 'text-brand' : 'text-ink/60 hover:text-brand' }}">
            <i class="ph-bold ph-house text-[21px]"></i>
            <span class="text-[10px] font-bold tracking-tight">{{ __('Home') }}</span>
        </a>

        {{-- Shop --}}
        <a href="{{ route('shop.index') }}" 
           class="flex flex-col items-center justify-center gap-0.5 h-full transition-all duration-150 active:scale-95 {{ request()->routeIs('shop.index') ? 'text-brand' : 'text-ink/60 hover:text-brand' }}">
            <i class="ph-bold ph-storefront text-[21px]"></i>
            <span class="text-[10px] font-bold tracking-tight">{{ __('Shop') }}</span>
        </a>

        {{-- Search Toggle --}}
        <button data-search-toggle 
                class="flex flex-col items-center justify-center gap-0.5 h-full w-full transition-all duration-150 active:scale-95 text-ink/60 hover:text-brand cursor-pointer"
                aria-label="Search">
            <i class="ph-bold ph-magnifying-glass text-[21px]"></i>
            <span class="text-[10px] font-bold tracking-tight">{{ __('Search') }}</span>
        </button>

        {{-- Wishlist --}}
        <a href="{{ route('shop.wishlist.index') }}" 
           class="flex flex-col items-center justify-center gap-0.5 h-full transition-all duration-150 active:scale-95 {{ request()->routeIs('shop.wishlist.index') ? 'text-brand' : 'text-ink/60 hover:text-brand' }}">
            <div class="relative">
                <i class="ph-bold ph-heart text-[21px]"></i>
                <span data-wishlist-count 
                      class="{{ $wishlistCount ? '' : 'hidden' }} absolute -top-1.5 -right-2 bg-brand text-white text-[9px] font-black w-4.5 h-4.5 rounded-full flex items-center justify-center shadow-sm">
                    {{ $wishlistCount }}
                </span>
            </div>
            <span class="text-[10px] font-bold tracking-tight">{{ __('Wishlist') }}</span>
        </a>

        {{-- Menu Toggle --}}
        <button data-menu-toggle 
                class="flex flex-col items-center justify-center gap-0.5 h-full w-full transition-all duration-150 active:scale-95 text-ink/60 hover:text-brand cursor-pointer"
                aria-label="Toggle menu">
            <i class="ph-bold ph-list text-[21px]"></i>
            <span class="text-[10px] font-bold tracking-tight">{{ __('Menu') }}</span>
        </button>

    </div>
</div>
