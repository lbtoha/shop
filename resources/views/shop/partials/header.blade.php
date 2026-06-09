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

<header class="sticky top-0 z-40">
    {{-- Top bar (green) --}}
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

    {{-- Main bar --}}
    <div class="bg-white border-b border-[color:var(--color-line)]">
        <div class="shop-container flex items-center gap-4 h-20">
            <button data-menu-toggle class="lg:hidden text-2xl" aria-label="Menu"><i class="ph ph-list"></i></button>

            <a href="{{ route('home') }}" class="text-2xl font-extrabold tracking-tight text-[color:var(--color-brand)] shrink-0">
                {{ $company['name'] }}
            </a>

            {{-- Big search --}}
            <form action="{{ route('shop.index') }}" method="GET" class="hidden md:flex flex-1 max-w-2xl mx-auto">
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('Search for the Items') }}"
                        class="w-full border border-[color:var(--color-line)] rounded-full py-3 pl-5 pr-14 text-sm focus:outline-none focus:border-[color:var(--color-brand)]">
                    <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 size-9 rounded-full bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white inline-flex items-center justify-center">
                        <i class="ph ph-magnifying-glass"></i>
                    </button>
                </div>
            </form>

            {{-- Right: account + cart --}}
            <div class="flex items-center gap-5 shrink-0 ml-auto">
                @auth
                    <a href="{{ route('shop.account.index') }}" class="hidden sm:flex items-center gap-2 text-sm hover:text-[color:var(--color-brand)]">
                        <i class="ph ph-user text-2xl"></i>
                        <span class="hidden lg:block leading-tight">
                            <span class="block text-xs text-[color:var(--color-muted)]">{{ __('Account') }}</span>
                            <span class="block font-medium text-[color:var(--color-ink)]">{{ \Illuminate\Support\Str::limit(auth()->user()->first_name ?? __('My Account'), 10) }}</span>
                        </span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:flex items-center gap-2 text-sm hover:text-[color:var(--color-brand)]">
                        <i class="ph ph-user text-2xl"></i>
                        <span class="hidden lg:block leading-tight">
                            <span class="block text-xs text-[color:var(--color-muted)]">{{ __('Account') }}</span>
                            <span class="block font-medium text-[color:var(--color-ink)]">{{ __('Sign In') }}</span>
                        </span>
                    </a>
                @endauth

                <a href="{{ route('shop.cart.index') }}" data-cart-open class="flex items-center gap-2 hover:text-[color:var(--color-brand)]">
                    <span class="relative">
                        <i class="ph ph-shopping-cart-simple text-2xl text-[color:var(--color-ink)]"></i>
                        <span data-cart-count
                            class="{{ $cartCount ? '' : 'hidden' }} absolute -top-2 -right-2 bg-[color:var(--color-brand)] text-white text-[10px] leading-none w-5 h-5 rounded-full flex items-center justify-center">
                            {{ $cartCount }}
                        </span>
                    </span>
                    <span class="hidden lg:block leading-tight">
                        <span class="block text-xs text-[color:var(--color-muted)]">{{ __('My Cart') }}</span>
                        <span class="block font-medium text-[color:var(--color-ink)]">{{ amountWithSymbol($cartSubtotal) }}</span>
                    </span>
                </a>
            </div>
        </div>
    </div>

    {{-- Category nav --}}
    <nav class="bg-white border-b border-[color:var(--color-line)] hidden lg:block">
        <div class="shop-container flex items-center gap-7 h-12 text-sm font-medium overflow-x-auto no-scrollbar">
            <a href="{{ route('home') }}" class="hover:text-[color:var(--color-brand)] whitespace-nowrap">{{ __('Home') }}</a>
            <a href="{{ route('shop.index') }}" class="hover:text-[color:var(--color-brand)] whitespace-nowrap">{{ __('Shop') }}</a>
            @foreach ($navCategories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                    class="hover:text-[color:var(--color-brand)] whitespace-nowrap">{{ $category->name }}</a>
            @endforeach
            <span class="ml-auto flex items-center gap-2 text-[color:var(--color-brand)] whitespace-nowrap">
                <i class="ph ph-truck"></i> {{ __('Cash on Delivery available') }}
            </span>
        </div>
    </nav>

    {{-- Mobile menu --}}
    <nav data-mobile-menu class="hidden lg:hidden border-b border-[color:var(--color-line)] bg-white">
        <form action="{{ route('shop.index') }}" method="GET" class="p-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search...') }}"
                class="w-full border border-[color:var(--color-line)] rounded-full py-2 px-4 text-sm focus:outline-none focus:border-[color:var(--color-brand)]">
        </form>
        <div class="px-4 pb-2 flex flex-col gap-1 text-sm font-medium">
            <a href="{{ route('home') }}" class="py-2">{{ __('Home') }}</a>
            <a href="{{ route('shop.index') }}" class="py-2">{{ __('Shop') }}</a>
            @foreach ($navCategories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="py-2">{{ $category->name }}</a>
            @endforeach
            <div class="flex items-center gap-2 py-2 border-t border-[color:var(--color-line)] mt-1 pt-3">
                <i class="ph ph-globe text-[color:var(--color-brand)]"></i>
                <a href="{{ route('shop.language', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'text-[color:var(--color-brand)] font-semibold' : '' }}">EN</a>
                <span class="text-[color:var(--color-muted)]">|</span>
                <a href="{{ route('shop.language', 'bn') }}" class="{{ app()->getLocale() === 'bn' ? 'text-[color:var(--color-brand)] font-semibold' : '' }}">বাংলা</a>
            </div>
        </div>
    </nav>
</header>
