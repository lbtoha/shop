@php
    $navCategories = \App\Models\Category::active()
        ->whereNull('parent_id')
        ->orderBy('sort_order')
        ->take(8)
        ->get();
    $company = config('application_info.company_info');
    $cart = app(\App\Services\Ecommerce\Cart::class);
    $cartCount = $cart->count();
    $cartSubtotal = $cart->subtotal();
@endphp

{{-- Top bar (green) - scrolls out of view naturally --}}
<div class="bg-[color:var(--color-brand)] text-white text-sm">
    <div class="shop-container flex items-center justify-center xl:justify-between h-11">
        <p class="hidden xl:flex items-center gap-x-2">
            <i class="ph ph-headset text-lg"></i>
            {{ __('Need Support? Call Us') }}
            <a href="tel:{{ $company['phone'] }}" class="bg-[color:var(--color-accent)] text-white py-0.5 px-2 text-xs rounded-full">{{ $company['phone'] }}</a>
        </p>
        <div class="flex items-center gap-x-5">
            @php($locale = app()->getLocale())
            <span class="hidden sm:inline-flex items-center gap-x-1.5">
                <span class="inline-flex items-center justify-center size-6 bg-[color:var(--color-brand-dark)] rounded-full"><i class="ph ph-globe"></i></span>
                <a href="{{ route('shop.language', 'en') }}" class="hover:underline {{ $locale === 'en' ? 'font-semibold' : 'opacity-70' }}">EN</a>
                <span class="opacity-40">|</span>
                <a href="{{ route('shop.language', 'bn') }}" class="hover:underline {{ $locale === 'bn' ? 'font-semibold' : 'opacity-70' }}">বাংলা</a>
            </span>
            <span class="hidden sm:inline w-px h-4 bg-white/30"></span>
            @auth
                <a href="{{ route('shop.account.index') }}" class="hover:underline">{{ __('My Account') }}</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">@csrf<button class="hover:underline">{{ __('Logout') }}</button></form>
            @else
                <a href="{{ route('login') }}" class="hover:underline">{{ __('Sign In') }}</a>
                <span class="text-white/40">/</span>
                <a href="{{ route('register') }}" class="hover:underline">{{ __('Sign Up') }}</a>
            @endauth
        </div>
    </div>
</div>

{{-- Sticky main navbar --}}
<header class="sticky top-0 z-40 bg-white border-b border-[color:var(--color-line)] shadow-sm">
    <div class="shop-container flex items-center justify-between h-16 sm:h-20 gap-4">
        {{-- Left: Menu toggle (mobile) & Logo --}}
        <div class="flex items-center gap-4 shrink-0">
            <button data-menu-toggle class="lg:hidden text-2xl text-[color:var(--color-ink)]" aria-label="Menu">
                <i class="ph ph-list"></i>
            </button>
            <a href="{{ route('home') }}" class="text-xl sm:text-2xl font-extrabold tracking-tight text-[color:var(--color-brand)] shrink-0">
                {{ $company['name'] }}
            </a>
        </div>

        {{-- Middle: Navigation Links (hidden on mobile, flex on desktop) --}}
        <nav class="hidden lg:flex items-center gap-5 xl:gap-7 text-sm font-semibold">
            <a href="{{ route('home') }}" class="hover:text-[color:var(--color-brand)] whitespace-nowrap transition-colors py-2">{{ __('Home') }}</a>
            <a href="{{ route('shop.index') }}" class="hover:text-[color:var(--color-brand)] whitespace-nowrap transition-colors py-2">{{ __('Shop') }}</a>

            {{-- On LG (1024px - 1280px): Show only Home, Shop, and a general Categories dropdown --}}
            <div class="relative group lg:block xl:hidden">
                <button class="flex items-center gap-1 hover:text-[color:var(--color-brand)] transition-colors py-3 focus:outline-none">
                    <span>{{ __('Categories') }}</span>
                    <i class="ph ph-caret-down text-xs transition-transform duration-200 group-hover:rotate-180"></i>
                </button>
                <div class="absolute top-full left-0 w-56 pt-2 opacity-0 translate-y-2 pointer-events-none group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto transition-all duration-200 z-50">
                    <div class="bg-white border border-[color:var(--color-line)] rounded-2xl shadow-xl py-2">
                        @foreach ($navCategories as $category)
                            <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                               class="block px-5 py-2.5 hover:bg-neutral-50 hover:text-[color:var(--color-brand)] transition-colors text-xs font-semibold">
                               {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- On XL/2XL: Show individual links, plus a dropdown for the rest --}}
            @foreach ($navCategories as $index => $category)
                @if ($index < 3)
                    {{-- Show first 3 categories on XL+ --}}
                    <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                       class="hidden xl:inline-block hover:text-[color:var(--color-brand)] whitespace-nowrap transition-colors py-2">
                       {{ $category->name }}
                    </a>
                @elseif ($index < 5)
                    {{-- Show 4th and 5th categories on 2XL+ --}}
                    <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                       class="hidden 2xl:inline-block hover:text-[color:var(--color-brand)] whitespace-nowrap transition-colors py-2">
                       {{ $category->name }}
                    </a>
                @endif
            @endforeach

            {{-- Dropdown for remaining categories on XL/2XL --}}
            <div class="relative group hidden xl:block">
                <button class="flex items-center gap-1 hover:text-[color:var(--color-brand)] transition-colors py-3 focus:outline-none">
                    <span>{{ __('More') }}</span>
                    <i class="ph ph-caret-down text-xs transition-transform duration-200 group-hover:rotate-180"></i>
                </button>
                <div class="absolute top-full left-0 w-56 pt-2 opacity-0 translate-y-2 pointer-events-none group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto transition-all duration-200 z-50">
                    <div class="bg-white border border-[color:var(--color-line)] rounded-2xl shadow-xl py-2">
                        @foreach ($navCategories as $index => $category)
                            <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                               class="px-5 py-2.5 hover:bg-neutral-50 hover:text-[color:var(--color-brand)] transition-colors text-xs font-semibold
                                      {{ $index < 3 ? 'xl:hidden' : 'block' }}
                                      {{ $index < 5 ? '2xl:hidden' : 'block' }}">
                               {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </nav>

        {{-- Right: Compact Search, Account & Cart --}}
        <div class="flex items-center gap-3 sm:gap-5 shrink-0 ml-auto lg:ml-0">
            {{-- Compact Search Bar --}}
            <form action="{{ route('shop.index') }}" method="GET" class="hidden md:flex relative w-40 lg:w-48 xl:w-56 focus-within:w-48 xl:focus-within:w-64 transition-all duration-300">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="{{ __('Search...') }}"
                    class="w-full border border-[color:var(--color-line)] rounded-full py-2 pl-4 pr-10 text-xs focus:outline-none focus:border-[color:var(--color-brand)] focus:ring-1 focus:ring-[color:var(--color-brand)] transition-colors">
                <button type="submit" class="absolute right-1 top-1/2 -translate-y-1/2 size-7 rounded-full bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white inline-flex items-center justify-center transition-colors">
                    <i class="ph ph-magnifying-glass text-xs"></i>
                </button>
            </form>

            {{-- Account --}}
            @auth
                <a href="{{ route('shop.account.index') }}" class="flex items-center gap-2 text-sm hover:text-[color:var(--color-brand)] transition-colors">
                    <i class="ph ph-user text-2xl text-[color:var(--color-ink)]"></i>
                    <span class="hidden xl:block leading-tight text-left">
                        <span class="block text-[10px] text-[color:var(--color-muted)] font-normal">{{ __('Account') }}</span>
                        <span class="block font-bold text-xs text-[color:var(--color-ink)]">{{ \Illuminate\Support\Str::limit(auth()->user()->first_name ?? __('My Account'), 8) }}</span>
                    </span>
                </a>
            @else
                <a href="{{ route('login') }}" class="flex items-center gap-2 text-sm hover:text-[color:var(--color-brand)] transition-colors">
                    <i class="ph ph-user text-2xl text-[color:var(--color-ink)]"></i>
                    <span class="hidden xl:block leading-tight text-left">
                        <span class="block text-[10px] text-[color:var(--color-muted)] font-normal">{{ __('Account') }}</span>
                        <span class="block font-bold text-xs text-[color:var(--color-ink)]">{{ __('Sign In') }}</span>
                    </span>
                </a>
            @endauth

            {{-- Cart --}}
            <a href="{{ route('shop.cart.index') }}" data-cart-open class="flex items-center gap-2 hover:text-[color:var(--color-brand)] transition-colors">
                <span class="relative">
                    <i class="ph ph-shopping-cart-simple text-2xl text-[color:var(--color-ink)]"></i>
                    <span data-cart-count
                        class="{{ $cartCount ? '' : 'hidden' }} absolute -top-1.5 -right-1.5 bg-[color:var(--color-brand)] text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center transition-all duration-300">
                        {{ $cartCount }}
                    </span>
                </span>
                <span class="hidden xl:block leading-tight text-left">
                    <span class="block text-[10px] text-[color:var(--color-muted)] font-normal">{{ __('My Cart') }}</span>
                    <span class="block font-bold text-xs text-[color:var(--color-ink)]">{{ amountWithSymbol($cartSubtotal) }}</span>
                </span>
            </a>
        </div>
    </div>

    {{-- Mobile menu --}}
    <nav data-mobile-menu class="hidden lg:hidden border-t border-[color:var(--color-line)] bg-white max-h-[75vh] overflow-y-auto">
        <form action="{{ route('shop.index') }}" method="GET" class="p-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search...') }}"
                class="w-full border border-[color:var(--color-line)] rounded-full py-2 px-4 text-sm focus:outline-none focus:border-[color:var(--color-brand)]">
        </form>
        <div class="px-4 pb-4 flex flex-col gap-1 text-sm font-semibold">
            <a href="{{ route('home') }}" class="py-2 border-b border-neutral-50">{{ __('Home') }}</a>
            <a href="{{ route('shop.index') }}" class="py-2 border-b border-neutral-50">{{ __('Shop') }}</a>
            @foreach ($navCategories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="py-2 border-b border-neutral-50">{{ $category->name }}</a>
            @endforeach
            <div class="flex items-center gap-2 py-3 mt-1">
                <i class="ph ph-globe text-[color:var(--color-brand)]"></i>
                <a href="{{ route('shop.language', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'text-[color:var(--color-brand)] font-semibold' : '' }}">EN</a>
                <span class="text-[color:var(--color-muted)]">|</span>
                <a href="{{ route('shop.language', 'bn') }}" class="{{ app()->getLocale() === 'bn' ? 'text-[color:var(--color-brand)] font-semibold' : '' }}">বাংলা</a>
            </div>
        </div>
    </nav>
</header>
