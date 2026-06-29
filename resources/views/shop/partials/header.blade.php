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
<div class="hidden sm:block bg-gradient-to-r from-ink/95 via-ink to-ink/95 border-b border-white/5 text-white/90 text-xs">
    <div class="shop-container flex items-center justify-between h-10">
        {{-- Left: Social Media Links --}}
        <div class="flex items-center gap-3">
            @foreach (config('application_info.social_medias', []) as $social)
                @if(!empty($social['link']) && $social['link'] !== '#')
                    <a href="{{ $social['link'] }}" target="_blank" rel="noopener" 
                       class="text-white/60 hover:text-brand-light transition-all duration-200 flex items-center hover:scale-110" 
                       title="{{ __($social['name']) }}">
                        <i class="{{ $social['icon'] }} text-xs sm:text-sm"></i>
                    </a>
                @endif
            @endforeach
            @php
                $companyTemp = config('application_info.company_info');
                $waNumberTemp = preg_replace('/\D/', '', ((int) getOption('whatsapp_enabled', 0) === 1 ? getOption('whatsapp_number') : null) ?: ($companyTemp['phone'] ?? ''));
            @endphp
            @if(!empty($waNumberTemp))
                <a href="https://wa.me/{{ $waNumberTemp }}" target="_blank" rel="noopener" 
                   class="text-white/60 hover:text-brand-light transition-all duration-200 flex items-center hover:scale-110" 
                   title="WhatsApp">
                    <i class="ph ph-whatsapp-logo text-xs sm:text-sm"></i>
                </a>
            @endif
        </div>

        {{-- Right: language + auth --}}
        <div class="flex items-center gap-4">
            @php
            $locale = app()->getLocale();
            @endphp
            
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
                <div class="flex items-center gap-3.5 text-[10px] sm:text-[11px] font-bold tracking-wider uppercase whitespace-nowrap">
                    <a href="{{ route('shop.account.index') }}" class="text-white/80 hover:text-brand-light transition-colors flex items-center gap-1 whitespace-nowrap">
                        <i class="ph-bold ph-user-circle text-sm text-brand-light"></i>
                        {{ __('My Account') }}
                    </a>
                    <span class="w-1 h-1 rounded-full bg-white/20"></span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button class="text-white/80 hover:text-brand-light transition-colors flex items-center gap-1 whitespace-nowrap">
                            <i class="ph-bold ph-sign-out text-sm text-white/40"></i>
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            @else
                <div class="flex items-center gap-3.5 text-[10px] sm:text-[11px] font-bold tracking-wider uppercase whitespace-nowrap">
                    <a href="{{ route('login') }}" class="text-white/80 hover:text-brand-light transition-colors flex items-center gap-1.5 whitespace-nowrap">
                        <i class="ph-bold ph-sign-in text-sm text-brand-light"></i>
                        {{ __('Sign In') }}
                    </a>
                    <span class="w-1 h-1 rounded-full bg-white/20"></span>
                    <a href="{{ route('register') }}" class="text-white/80 hover:text-brand-light transition-colors flex items-center gap-1.5 whitespace-nowrap">
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
            <a href="{{ route('home') }}"
               class="flex items-center shrink-0 group">
                <img src="{{ asset(config('application_info.logo_favicon.logo_light', 'assets/logo.png')) }}"
                     alt="{{ $company['name'] }}"
                     class="h-14 sm:h-16 w-auto object-contain transition-opacity duration-200 group-hover:opacity-80">
            </a>
        </div>

        {{-- Desktop navigation --}}
        <nav class="hidden lg:flex items-center gap-1 text-md font-medium flex-1 justify-center">
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
                    class="w-36 lg:w-44 xl:w-56 h-9.5 sm:h-11 border border-line rounded-full pl-5 pr-11 sm:pr-12 text-xs sm:text-sm text-ink placeholder:text-muted focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand/15 transition-all duration-200 bg-canvas focus:bg-white">
                <button type="submit"
                    class="absolute right-1.5 top-1/2 -translate-y-1/2 w-6.5 h-6.5 sm:w-8 sm:h-8 rounded-full bg-brand hover:bg-brand-dark text-white flex items-center justify-center transition-all duration-200">
                    <i class="ph-bold ph-magnifying-glass text-xs sm:text-sm"></i>
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

</header>
