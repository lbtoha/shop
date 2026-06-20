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
<div class="bg-ink text-white text-xs">
    <div class="shop-container flex items-center justify-between h-9">
        {{-- Left: promo text --}}
        <p class="hidden sm:flex items-center gap-2 text-white/70 font-medium tracking-wide">
            <i class="ph ph-truck text-brand-light text-sm"></i>
            {{ __('Free delivery on orders above ৳5000') }}
        </p>
        <p class="sm:hidden text-white/70 font-medium tracking-wide">
            {{ $company['name'] }}
        </p>

        {{-- Right: language + auth --}}
        <div class="flex items-center gap-4 text-white/80">
            @php($locale = app()->getLocale())
            <span class="hidden sm:inline-flex items-center gap-1.5">
                <i class="ph ph-globe text-xs opacity-70"></i>
                <a href="{{ route('shop.language', 'en') }}"
                   class="hover:text-white transition-colors {{ $locale === 'en' ? 'text-white font-semibold' : 'opacity-60' }}">EN</a>
                <span class="opacity-30">|</span>
                <a href="{{ route('shop.language', 'bn') }}"
                   class="hover:text-white transition-colors {{ $locale === 'bn' ? 'text-white font-semibold' : 'opacity-60' }}">বাংলা</a>
            </span>
            <span class="hidden sm:block w-px h-3 bg-white/20"></span>
            @auth
                <a href="{{ route('shop.account.index') }}" class="hover:text-white transition-colors">{{ __('My Account') }}</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button class="hover:text-white transition-colors">{{ __('Logout') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hover:text-white transition-colors">{{ __('Sign In') }}</a>
                <span class="opacity-30">/</span>
                <a href="{{ route('register') }}" class="hover:text-white transition-colors">{{ __('Sign Up') }}</a>
            @endauth
        </div>
    </div>
</div>

{{-- ── Main sticky header ───────────────────────────────────── --}}
<header class="sticky top-0 z-40 bg-white border-b border-line shadow-[0_2px_12px_rgba(0,0,0,.06)]">
    <div class="shop-container flex items-center justify-between gap-4 h-[68px] sm:h-20">

        {{-- Logo + mobile toggle --}}
        <div class="flex items-center gap-3 shrink-0">
            <button data-menu-toggle
                class="lg:hidden w-9 h-9 rounded-lg flex items-center justify-center text-ink hover:bg-canvas transition-colors"
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
               class="px-3 py-2 rounded-lg hover:bg-brand-soft hover:text-brand transition-all duration-150 whitespace-nowrap">
               {{ __('Home') }}
            </a>
            <a href="{{ route('shop.index') }}"
               class="px-3 py-2 rounded-lg hover:bg-brand-soft hover:text-brand transition-all duration-150 whitespace-nowrap">
               {{ __('Shop') }}
            </a>

            {{-- Categories dropdown --}}
            <div class="relative group">
                <button class="flex items-center gap-1 px-3 py-2 rounded-lg hover:bg-brand-soft hover:text-brand transition-all duration-150 focus:outline-none whitespace-nowrap">
                    {{ __('Categories') }}
                    <i class="ph ph-caret-down text-xs transition-transform duration-200 group-hover:rotate-180"></i>
                </button>
                <div class="absolute top-full left-1/2 -translate-x-1/2 w-52 pt-2 opacity-0 translate-y-1 pointer-events-none group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto transition-all duration-200 z-50">
                    <div class="bg-white border border-line rounded-2xl shadow-2xl shadow-ink/10 py-1.5 overflow-hidden">
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
                    class="w-36 lg:w-44 xl:w-52 border border-line rounded-xl py-2 pl-4 pr-10 text-xs text-ink placeholder:text-subtle focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand/15 transition-all duration-200 bg-canvas focus:bg-white">
                <button type="submit"
                    class="absolute right-1.5 top-1/2 -translate-y-1/2 w-6 h-6 rounded-lg bg-brand hover:bg-brand-dark text-white flex items-center justify-center transition-colors">
                    <i class="ph ph-magnifying-glass text-xs"></i>
                </button>
            </form>

            {{-- Account icon --}}
            @auth
                <a href="{{ route('shop.account.index') }}"
                   class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-brand-soft group transition-colors">
                    <span class="w-8 h-8 rounded-xl bg-brand/10 text-brand flex items-center justify-center group-hover:bg-brand group-hover:text-white transition-all duration-200 shrink-0">
                        <i class="ph ph-user text-base"></i>
                    </span>
                    <span class="hidden xl:block leading-none text-left">
                        <span class="block text-[10px] text-subtle font-normal">{{ __('Account') }}</span>
                        <span class="block text-xs font-bold text-ink group-hover:text-brand transition-colors">
                            {{ \Illuminate\Support\Str::limit(auth()->user()->first_name ?? 'Account', 8) }}
                        </span>
                    </span>
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-brand-soft group transition-colors">
                    <span class="w-8 h-8 rounded-xl bg-canvas text-muted flex items-center justify-center group-hover:bg-brand group-hover:text-white transition-all duration-200 shrink-0 border border-line group-hover:border-brand">
                        <i class="ph ph-user text-base"></i>
                    </span>
                    <span class="hidden xl:block leading-none text-left">
                        <span class="block text-[10px] text-subtle font-normal">{{ __('Account') }}</span>
                        <span class="block text-xs font-bold text-ink group-hover:text-brand transition-colors">{{ __('Sign In') }}</span>
                    </span>
                </a>
            @endauth

            {{-- Wishlist --}}
            <a href="{{ route('shop.wishlist.index') }}"
                class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-brand-soft group transition-colors cursor-pointer">
                <span class="relative">
                    <span class="w-8 h-8 rounded-xl bg-canvas text-muted flex items-center justify-center group-hover:bg-brand group-hover:text-white transition-all duration-200 shrink-0 border border-line group-hover:border-brand">
                        <i class="ph ph-heart text-base"></i>
                    </span>
                    <span data-wishlist-count
                        class="{{ $wishlistCount ? '' : 'hidden' }} absolute -top-1.5 -right-1.5 bg-accent text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center shadow-sm transition-all duration-300">
                        {{ $wishlistCount }}
                    </span>
                </span>
                <span class="hidden xl:block leading-none text-left">
                    <span class="block text-[10px] text-subtle font-normal">{{ __('Wishlist') }}</span>
                    <span class="block text-xs font-bold text-ink group-hover:text-brand transition-colors">{{ __('View') }}</span>
                </span>
            </a>

            {{-- Cart --}}
            <button data-cart-open
                class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-brand-soft group transition-colors cursor-pointer">
                <span class="relative">
                    <span class="w-8 h-8 rounded-xl bg-brand text-white flex items-center justify-center group-hover:bg-brand-dark transition-colors shadow-sm shadow-brand/30">
                        <i class="ph ph-shopping-cart-simple text-base"></i>
                    </span>
                    <span data-cart-count
                        class="{{ $cartCount ? '' : 'hidden' }} absolute -top-1.5 -right-1.5 bg-accent text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center shadow-sm transition-all duration-300">
                        {{ $cartCount }}
                    </span>
                </span>
                <span class="hidden xl:block leading-none text-left">
                    <span class="block text-[10px] text-subtle font-normal">{{ __('My Cart') }}</span>
                    <span class="block text-xs font-bold text-ink group-hover:text-brand transition-colors">{{ amountWithSymbol($cartSubtotal) }}</span>
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
                        class="w-full border border-line rounded-xl py-2.5 pl-4 pr-11 text-sm focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand/15 bg-canvas">
                    <button type="submit"
                        class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg bg-brand hover:bg-brand-dark text-white flex items-center justify-center transition-colors">
                        <i class="ph ph-magnifying-glass text-sm"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- Nav links --}}
        <div class="px-4 py-3 flex flex-col gap-0.5 text-sm font-semibold">
            <a href="{{ route('home') }}"
               class="flex items-center gap-3 py-2.5 px-3 rounded-xl hover:bg-brand-soft hover:text-brand transition-colors">
               <i class="ph ph-house text-base text-brand/60"></i>{{ __('Home') }}
            </a>
            <a href="{{ route('shop.index') }}"
               class="flex items-center gap-3 py-2.5 px-3 rounded-xl hover:bg-brand-soft hover:text-brand transition-colors">
               <i class="ph ph-storefront text-base text-brand/60"></i>{{ __('Shop') }}
            </a>
            <a href="{{ route('shop.wishlist.index') }}"
               class="flex items-center gap-3 py-2.5 px-3 rounded-xl hover:bg-brand-soft hover:text-brand transition-colors">
               <i class="ph ph-heart text-base text-brand/60"></i>{{ __('Wishlist') }}
               <span data-wishlist-count class="{{ $wishlistCount ? '' : 'hidden' }} ml-auto bg-accent text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                   {{ $wishlistCount }}
               </span>
            </a>

            @if ($navCategories->isNotEmpty())
                <div class="mt-1 mb-0.5 px-3">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-subtle">{{ __('Categories') }}</span>
                </div>
                @foreach ($navCategories as $cat)
                    <a href="{{ route('shop.index', ['category' => $cat->slug]) }}"
                       class="flex items-center gap-3 py-2.5 px-3 rounded-xl hover:bg-brand-soft hover:text-brand transition-colors text-body">
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
