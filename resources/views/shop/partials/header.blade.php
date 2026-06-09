@php
    $navCategories = \App\Models\Category::active()
        ->whereNull('parent_id')
        ->orderBy('sort_order')
        ->take(8)
        ->get();
    $company = config('application_info.company_info');
    $cartCount = app(\App\Services\Ecommerce\Cart::class)->count();
@endphp

<header class="bg-white sticky top-0 z-40">
    {{-- Top utility bar --}}
    <div class="bg-[color:var(--color-ink)] text-neutral-300 text-xs">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-9">
            <span class="hidden sm:block">{{ __('Welcome to') }} {{ $company['name'] }} — {{ __('Cash on Delivery available') }}</span>
            <div class="flex items-center gap-4 ml-auto">
                @auth
                    <a href="{{ route('shop.account.index') }}" class="hover:text-white">{{ __('My Account') }}</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">@csrf<button class="hover:text-white">{{ __('Logout') }}</button></form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-white">{{ __('Login') }}</a>
                    <span class="text-neutral-600">/</span>
                    <a href="{{ route('register') }}" class="hover:text-white">{{ __('Sign Up') }}</a>
                @endauth
                <span class="hidden sm:inline text-neutral-600">|</span>
                <a href="tel:{{ $company['phone'] }}" class="hidden sm:flex items-center gap-1 hover:text-white">
                    <i class="ph ph-phone-call"></i> {{ $company['phone'] }}
                </a>
            </div>
        </div>
    </div>

    {{-- Main bar --}}
    <div class="border-b border-[color:var(--color-line)]">
        <div class="max-w-7xl mx-auto px-4 flex items-center gap-4 h-20">
            <button data-menu-toggle class="lg:hidden text-2xl" aria-label="Menu"><i class="ph ph-list"></i></button>

            <a href="{{ route('home') }}" class="text-2xl font-extrabold tracking-tight text-[color:var(--color-brand)] shrink-0">
                {{ $company['name'] }}
            </a>

            {{-- Centered search --}}
            <form action="{{ route('shop.index') }}" method="GET" class="hidden md:flex flex-1 max-w-2xl mx-auto">
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('Search for products...') }}"
                        class="w-full border border-[color:var(--color-line)] rounded-md py-2.5 pl-4 pr-12 text-sm focus:outline-none focus:border-[color:var(--color-brand)]">
                    <button type="submit" class="absolute right-0 top-0 h-full px-4 bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white rounded-r-md">
                        <i class="ph ph-magnifying-glass"></i>
                    </button>
                </div>
            </form>

            {{-- Right icons --}}
            <div class="flex items-center gap-5 shrink-0 ml-auto">
                @auth
                    <a href="{{ route('shop.account.index') }}" class="hidden sm:flex flex-col items-center text-xs text-[color:var(--color-body)] hover:text-[color:var(--color-brand)]">
                        <i class="ph ph-user text-2xl"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:flex flex-col items-center text-xs text-[color:var(--color-body)] hover:text-[color:var(--color-brand)]">
                        <i class="ph ph-user text-2xl"></i>
                    </a>
                @endauth
                <a href="{{ route('shop.cart.index') }}" class="relative text-[color:var(--color-ink)] hover:text-[color:var(--color-brand)]">
                    <i class="ph ph-shopping-cart-simple text-2xl"></i>
                    <span data-cart-count
                        class="{{ $cartCount ? '' : 'hidden' }} absolute -top-2 -right-2 bg-[color:var(--color-brand)] text-white text-[10px] leading-none w-5 h-5 rounded-full flex items-center justify-center">
                        {{ $cartCount }}
                    </span>
                </a>
            </div>
        </div>
    </div>

    {{-- Category nav --}}
    <nav class="border-b border-[color:var(--color-line)] hidden lg:block">
        <div class="max-w-7xl mx-auto px-4 flex items-center gap-7 h-12 text-sm font-medium overflow-x-auto no-scrollbar">
            <a href="{{ route('home') }}" class="hover:text-[color:var(--color-brand)] whitespace-nowrap">{{ __('Home') }}</a>
            <a href="{{ route('shop.index') }}" class="hover:text-[color:var(--color-brand)] whitespace-nowrap">{{ __('Shop') }}</a>
            @foreach ($navCategories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                    class="hover:text-[color:var(--color-brand)] whitespace-nowrap">{{ $category->name }}</a>
            @endforeach
            <span class="ml-auto flex items-center gap-2 text-[color:var(--color-brand)] whitespace-nowrap">
                <i class="ph ph-truck"></i> {{ __('Free delivery on selected items') }}
            </span>
        </div>
    </nav>

    {{-- Mobile menu --}}
    <nav data-mobile-menu class="hidden lg:hidden border-b border-[color:var(--color-line)] bg-white">
        <form action="{{ route('shop.index') }}" method="GET" class="p-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search...') }}"
                class="w-full border border-[color:var(--color-line)] rounded-md py-2 px-3 text-sm focus:outline-none focus:border-[color:var(--color-brand)]">
        </form>
        <div class="px-4 pb-2 flex flex-col gap-1 text-sm font-medium">
            <a href="{{ route('home') }}" class="py-2">{{ __('Home') }}</a>
            <a href="{{ route('shop.index') }}" class="py-2">{{ __('Shop') }}</a>
            @foreach ($navCategories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="py-2">{{ $category->name }}</a>
            @endforeach
        </div>
    </nav>
</header>
