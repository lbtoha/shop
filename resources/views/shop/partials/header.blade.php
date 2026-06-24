@php
    $navCategories = \App\Models\Category::active()
        ->whereNull('parent_id')
        ->orderBy('sort_order')
        ->take(8)
        ->get();
    $company = config('application_info.company_info');
    $cart    = app(\App\Services\Ecommerce\Cart::class);
    $cartCount    = $cart->count();
    $cartSubtotal = $cart->subtotal();
    $wishlistCount = app(\App\Services\Ecommerce\Wishlist::class)->count();
@endphp

{{-- ── Announcement / top bar ──────────────────────────────── --}}
<div class="bg-gradient-to-r from-ink/95 via-ink to-ink/95 border-b border-white/5 text-white/90 text-xs">
    <div class="shop-container flex items-center justify-between h-10">
        {{-- Left: promo text with pulsing live indicator --}}
        <div class="flex items-center gap-2 text-white/75 font-semibold tracking-wider text-[10px] sm:text-[11px] uppercase">
            <span class="flex h-1.5 w-1.5 relative shrink-0">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-light opacity-75"></span>
                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-brand-light"></span>
            </span>
            <i class="ph-bold ph-truck text-brand-light text-xs shrink-0"></i>
            <span class="truncate">{{ __('Free delivery on orders above ৳5000') }}</span>
        </div>

        {{-- Right: language + auth --}}
        <div class="flex items-center gap-4">
            @php($locale = app()->getLocale())
            
            {{-- Modern Language Toggle Pill --}}
            <div class="hidden sm:flex items-center gap-0.5 bg-white/5 p-0.5 rounded-md border border-white/10 text-[10px] font-bold">
                <a href="{{ route('shop.language', 'en') }}"
                   class="px-2 py-0.5 rounded transition-all {{ $locale === 'en' ? 'bg-brand text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">EN</a>
                <a href="{{ route('shop.language', 'bn') }}"
                   class="px-2 py-0.5 rounded transition-all {{ $locale === 'bn' ? 'bg-brand text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">বাংলা</a>
            </div>

            <span class="hidden sm:block w-px h-3 bg-white/10"></span>

            {{-- Auth / Account options with icons --}}
            @auth
                <div class="flex items-center gap-3.5 text-[10px] sm:text-[11px] font-bold tracking-wider uppercase">
                    <a href="{{ route('shop.account.index') }}" class="text-white/80 hover:text-brand-light transition-colors flex items-center gap-1">
                        <i class="ph-bold ph-user-circle text-sm text-brand-light"></i>
                        {{ __('My Account') }}
                    </a>
                    <span class="w-1 h-1 rounded-full bg-white/20"></span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button class="text-white/80 hover:text-brand-light transition-colors flex items-center gap-1">
                            <i class="ph-bold ph-sign-out text-sm text-white/40"></i>
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            @else
                <div class="flex items-center gap-3.5 text-[10px] sm:text-[11px] font-bold tracking-wider uppercase">
                    <a href="{{ route('login') }}" class="text-white/80 hover:text-brand-light transition-colors flex items-center gap-1.5">
                        <i class="ph-bold ph-sign-in text-sm text-brand-light"></i>
                        {{ __('Sign In') }}
                    </a>
                    <span class="w-1 h-1 rounded-full bg-white/20"></span>
                    <a href="{{ route('register') }}" class="text-white/80 hover:text-brand-light transition-colors flex items-center gap-1.5">
                        <i class="ph-bold ph-user-plus text-sm text-brand-light"></i>
                        {{ __('Sign Up') }}
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>

{{-- ── Main sticky header ───────────────────────────────────── --}}
<header id="main-header" class="sticky top-0 z-40 bg-white border-b border-line shadow-none transition-all duration-300">
    <div class="shop-container flex items-center justify-between gap-4 h-[68px] sm:h-20">

        {{-- Logo + mobile toggle --}}
        <div class="flex items-center gap-3 shrink-0">
            <button data-menu-toggle
                class="lg:hidden w-9 h-9 rounded-md flex items-center justify-center text-ink hover:bg-canvas transition-colors"
                aria-label="Toggle menu">
                <i class="ph ph-list text-2xl"></i>
            </button>
            <a href="{{ route('home') }}"
               class="flex items-center shrink-0 group">
                <img src="{{ asset('assets/logo.png') }}"
                     alt="{{ $company['name'] }}"
                     class="h-14 sm:h-16 w-auto object-contain transition-opacity duration-200 group-hover:opacity-80">
            </a>
        </div>

        {{-- Desktop navigation --}}
        <nav class="hidden lg:flex items-center gap-1 text-sm font-semibold flex-1 justify-center">
            <a href="{{ route('home') }}"
               class="px-3 py-2 rounded-md hover:bg-brand-soft hover:text-brand transition-all duration-150 whitespace-nowrap">
               {{ __('Home') }}
            </a>
            <a href="{{ route('shop.index') }}"
               class="px-3 py-2 rounded-md hover:bg-brand-soft hover:text-brand transition-all duration-150 whitespace-nowrap">
               {{ __('Shop') }}
            </a>
            <a href="{{ route('shop.track') }}"
               class="px-3 py-2 rounded-md hover:bg-brand-soft hover:text-brand transition-all duration-150 whitespace-nowrap">
               {{ __('Track Order') }}
            </a>

            {{-- Categories dropdown --}}
            <div class="relative group">
                <button class="flex items-center gap-1 px-3 py-2 rounded-md hover:bg-brand-soft hover:text-brand transition-all duration-150 focus:outline-none whitespace-nowrap">
                    {{ __('Categories') }}
                    <i class="ph ph-caret-down text-xs transition-transform duration-200 group-hover:rotate-180"></i>
                </button>
                <div class="absolute top-full left-1/2 -translate-x-1/2 w-52 pt-2 opacity-0 translate-y-1 pointer-events-none group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto transition-all duration-200 z-50">
                    <div class="bg-white border border-line rounded-md shadow-2xl shadow-ink/10 py-1.5 overflow-hidden">
                        @foreach ($navCategories as $cat)
                            <a href="{{ route('shop.index', ['category' => $cat->slug]) }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-xs hover:bg-brand-soft hover:text-brand transition-colors font-medium">
                               <i class="ph ph-tag text-brand/50 text-base"></i>
                               {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </nav>

        {{-- Right actions: search + account + cart --}}
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">

            {{-- Search --}}
            <form action="{{ route('shop.index') }}" method="GET"
                  class="hidden md:flex items-center relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="{{ __('Search products…') }}"
                    class="w-36 lg:w-44 xl:w-52 border border-line rounded-full py-2 pl-4 pr-10 text-xs text-ink placeholder:text-muted focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand/15 transition-all duration-200 bg-canvas focus:bg-white">
                <button type="submit"
                    class="absolute right-1.5 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-brand hover:bg-brand-dark text-white flex items-center justify-center transition-colors">
                    <i class="ph-bold ph-magnifying-glass text-xs"></i>
                </button>
            </form>

            {{-- Account icon --}}
            @auth
                <a href="{{ route('shop.account.index') }}"
                   class="w-9.5 h-9.5 sm:w-11 sm:h-11 rounded-full bg-canvas border border-line text-ink flex items-center justify-center hover:bg-brand hover:border-brand hover:text-white transition-all duration-200 shrink-0"
                   title="{{ __('Account') }}">
                    <i class="ph-bold ph-user text-lg sm:text-xl"></i>
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="w-9.5 h-9.5 sm:w-11 sm:h-11 rounded-full bg-canvas border border-line text-ink flex items-center justify-center hover:bg-brand hover:border-brand hover:text-white transition-all duration-200 shrink-0"
                   title="{{ __('Sign In') }}">
                    <i class="ph-bold ph-user text-lg sm:text-xl"></i>
                </a>
            @endauth

            {{-- Wishlist --}}
            <a href="{{ route('shop.wishlist.index') }}"
               class="hidden xs:flex relative w-9.5 h-9.5 sm:w-11 sm:h-11 rounded-full bg-canvas border border-line text-ink items-center justify-center hover:bg-brand hover:border-brand hover:text-white transition-all duration-200 shrink-0 cursor-pointer"
               title="{{ __('Wishlist') }}">
                <i class="ph-bold ph-heart text-lg sm:text-xl"></i>
                <span data-wishlist-count
                    class="{{ $wishlistCount ? '' : 'hidden' }} absolute -top-1 -right-1 bg-brand text-white text-[9px] font-black w-4.5 h-4.5 sm:w-5 sm:h-5 rounded-full flex items-center justify-center shadow-sm transition-all duration-300">
                    {{ $wishlistCount }}
                </span>
            </a>

            {{-- Cart --}}
            <button data-cart-open
                class="relative w-9.5 h-9.5 sm:w-11 sm:h-11 rounded-full bg-canvas border border-line text-ink flex items-center justify-center hover:bg-brand hover:border-brand hover:text-white transition-all duration-200 shrink-0 cursor-pointer"
                title="{{ __('My Cart') }}">
                <i class="ph-bold ph-shopping-cart-simple text-lg sm:text-xl"></i>
                <span data-cart-count
                    class="{{ $cartCount ? '' : 'hidden' }} absolute -top-1 -right-1 bg-brand text-white text-[9px] font-black w-4.5 h-4.5 sm:w-5 sm:h-5 rounded-full flex items-center justify-center shadow-sm transition-all duration-300">
                    {{ $cartCount }}
                </span>
            </button>
        </div>
    </div>

    {{-- ── Mobile drawer menu ───────────────────────────────── --}}
    <nav data-mobile-menu
        class="hidden lg:hidden bg-white border-t border-line max-h-[80vh] overflow-y-auto">

        {{-- Search --}}
        <div class="p-4 border-b border-line-soft">
            <form action="{{ route('shop.index') }}" method="GET">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('Search products…') }}"
                        class="w-full border border-line rounded-full py-2.5 pl-5 pr-12 text-sm focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand/15 bg-canvas">
                    <button type="submit"
                        class="absolute right-1.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-brand hover:bg-brand-dark text-white flex items-center justify-center transition-colors">
                        <i class="ph-bold ph-magnifying-glass text-sm"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- Nav links --}}
        <div class="px-4 py-3 flex flex-col gap-0.5 text-sm font-semibold">
            <a href="{{ route('home') }}"
               class="flex items-center gap-3 py-2.5 px-3 rounded-md hover:bg-brand-soft hover:text-brand transition-colors">
               <i class="ph ph-house text-base text-brand/60"></i>{{ __('Home') }}
            </a>
            <a href="{{ route('shop.index') }}"
               class="flex items-center gap-3 py-2.5 px-3 rounded-md hover:bg-brand-soft hover:text-brand transition-colors">
               <i class="ph ph-storefront text-base text-brand/60"></i>{{ __('Shop') }}
            </a>
            <a href="{{ route('shop.wishlist.index') }}"
               class="flex items-center gap-3 py-2.5 px-3 rounded-md hover:bg-brand-soft hover:text-brand transition-colors">
               <i class="ph ph-heart text-base text-brand/60"></i>{{ __('Wishlist') }}
               <span data-wishlist-count class="{{ $wishlistCount ? '' : 'hidden' }} ml-auto bg-accent text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                   {{ $wishlistCount }}
               </span>
            </a>
            <a href="{{ route('shop.track') }}"
               class="flex items-center gap-3 py-2.5 px-3 rounded-md hover:bg-brand-soft hover:text-brand transition-colors">
               <i class="ph ph-hash text-base text-brand/60"></i>{{ __('Track Order') }}
            </a>

            @if ($navCategories->isNotEmpty())
                <div class="mt-1 mb-0.5 px-3">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-subtle">{{ __('Categories') }}</span>
                </div>
                @foreach ($navCategories as $cat)
                    <a href="{{ route('shop.index', ['category' => $cat->slug]) }}"
                       class="flex items-center gap-3 py-2.5 px-3 rounded-md hover:bg-brand-soft hover:text-brand transition-colors text-body">
                       <i class="ph ph-tag text-base text-brand/40"></i>{{ $cat->name }}
                    </a>
                @endforeach
            @endif

            {{-- Language --}}
            <div class="mt-2 pt-3 border-t border-line-soft flex items-center gap-3 px-3 pb-1">
                <i class="ph ph-globe text-brand/60"></i>
                <a href="{{ route('shop.language', 'en') }}"
                   class="text-sm {{ app()->getLocale() === 'en' ? 'text-brand font-semibold' : 'text-muted' }} hover:text-brand transition-colors">EN</a>
                <span class="text-subtle">|</span>
                <a href="{{ route('shop.language', 'bn') }}"
                   class="text-sm {{ app()->getLocale() === 'bn' ? 'text-brand font-semibold' : 'text-muted' }} hover:text-brand transition-colors">বাংলা</a>
            </div>
        </div>
    </nav>
</header>
