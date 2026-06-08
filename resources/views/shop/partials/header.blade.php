@php
    $navCategories = \App\Models\Category::active()
        ->whereNull('parent_id')
        ->orderBy('sort_order')
        ->take(8)
        ->get();
    $company = config('application_info.company_info');
    $cartCount = app(\App\Services\Ecommerce\Cart::class)->count();
@endphp

<header class="bg-white shadow-sm sticky top-0 z-40">
    {{-- Top bar --}}
    <div class="border-b border-neutral-100">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-9 text-xs text-[color:var(--color-muted)]">
            <span class="hidden sm:block">{{ $company['description'] ? \Illuminate\Support\Str::limit($company['description'], 60) : '' }}</span>
            <div class="flex items-center gap-4 ml-auto">
                <a href="tel:{{ $company['phone'] }}" class="hidden sm:flex items-center gap-1 hover:text-[color:var(--color-brand)]">
                    <i class="ph ph-phone"></i> {{ $company['phone'] }}
                </a>
                @auth
                    <a href="{{ route('shop.account.index') }}" class="flex items-center gap-1 hover:text-[color:var(--color-brand)]">
                        <i class="ph ph-user-circle"></i> {{ __('My Account') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-[color:var(--color-brand)]">{{ __('Logout') }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-[color:var(--color-brand)]">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="hover:text-[color:var(--color-brand)]">{{ __('Register') }}</a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Main bar --}}
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-16 gap-4">
        <button data-menu-toggle class="lg:hidden text-2xl" aria-label="Menu">
            <i class="ph ph-list"></i>
        </button>

        <a href="{{ route('home') }}" class="text-xl font-bold tracking-tight text-[color:var(--color-brand)] shrink-0">
            {{ $company['name'] }}
        </a>

        {{-- Search --}}
        <form action="{{ route('shop.index') }}" method="GET" class="hidden md:flex flex-1 max-w-md mx-4">
            <div class="relative w-full">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="{{ __('Search products...') }}"
                    class="w-full border border-neutral-200 rounded-full py-2 pl-4 pr-10 text-sm focus:outline-none focus:border-[color:var(--color-brand)]">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400">
                    <i class="ph ph-magnifying-glass"></i>
                </button>
            </div>
        </form>

        <a href="{{ route('shop.cart.index') }}" class="relative text-2xl text-ink hover:text-[color:var(--color-brand)] shrink-0">
            <i class="ph ph-shopping-cart-simple"></i>
            <span data-cart-count
                class="{{ $cartCount ? '' : 'hidden' }} absolute -top-2 -right-2 bg-[color:var(--color-brand)] text-white text-[10px] leading-none w-5 h-5 rounded-full flex items-center justify-center">
                {{ $cartCount }}
            </span>
        </a>
    </div>

    {{-- Category nav (desktop) --}}
    <nav class="border-t border-neutral-100 hidden lg:block">
        <div class="max-w-7xl mx-auto px-4 flex items-center gap-6 h-11 text-sm font-medium overflow-x-auto no-scrollbar">
            <a href="{{ route('home') }}" class="hover:text-[color:var(--color-brand)] whitespace-nowrap">{{ __('Home') }}</a>
            <a href="{{ route('shop.index') }}" class="hover:text-[color:var(--color-brand)] whitespace-nowrap">{{ __('Shop') }}</a>
            @foreach ($navCategories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                    class="hover:text-[color:var(--color-brand)] whitespace-nowrap">{{ $category->name }}</a>
            @endforeach
        </div>
    </nav>

    {{-- Mobile menu --}}
    <nav data-mobile-menu class="hidden lg:hidden border-t border-neutral-100 bg-white">
        <div class="px-4 py-2 flex flex-col gap-1 text-sm font-medium">
            <a href="{{ route('home') }}" class="py-2">{{ __('Home') }}</a>
            <a href="{{ route('shop.index') }}" class="py-2">{{ __('Shop') }}</a>
            @foreach ($navCategories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="py-2">{{ $category->name }}</a>
            @endforeach
        </div>
    </nav>
</header>
