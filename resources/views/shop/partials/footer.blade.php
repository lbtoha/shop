@php
    $company = config('application_info.company_info');
    $address = config('application_info.address');
    $footerCategories = \App\Models\Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(5)->get();
@endphp

<footer class="bg-neutral-900 text-neutral-400 border-t border-neutral-800">
    <div class="shop-container py-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">
        {{-- Brand info --}}
        <div class="col-span-1 lg:col-span-2 space-y-4">
            <h3 class="text-white text-2xl font-extrabold tracking-tight">{{ $company['name'] }}</h3>
            <p class="text-sm leading-relaxed max-w-sm text-neutral-400">{{ $company['description'] }}</p>
            <div class="flex items-center gap-3 pt-3">
                @foreach ([
                    ['icon' => 'ph-facebook-logo', 'url' => '#'],
                    ['icon' => 'ph-instagram-logo', 'url' => '#'],
                    ['icon' => 'ph-twitter-logo', 'url' => '#'],
                    ['icon' => 'ph-youtube-logo', 'url' => '#']
                ] as $social)
                    <a href="{{ $social['url'] }}" class="w-10 h-10 rounded-xl bg-neutral-800 hover:bg-[color:var(--color-brand)] hover:text-white flex items-center justify-center text-neutral-300 transition-all duration-300 shadow-sm">
                        <i class="ph {{ $social['icon'] }} text-lg"></i>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- About Links --}}
        <div>
            <h5 class="text-white font-bold uppercase tracking-wider text-xs mb-5">{{ __('About Us') }}</h5>
            <ul class="space-y-3 text-sm">
                <li><a href="{{ route('home') }}" class="hover:text-white transition-colors duration-200">{{ __('Home') }}</a></li>
                <li><a href="{{ route('shop.index') }}" class="hover:text-white transition-colors duration-200">{{ __('Shop Products') }}</a></li>
                <li><a href="{{ route('shop.cart.index') }}" class="hover:text-white transition-colors duration-200">{{ __('Shopping Cart') }}</a></li>
            </ul>
        </div>

        {{-- Account Links --}}
        <div>
            <h5 class="text-white font-bold uppercase tracking-wider text-xs mb-5">{{ __('My Account') }}</h5>
            <ul class="space-y-3 text-sm">
                @auth
                    <li><a href="{{ route('shop.account.index') }}" class="hover:text-white transition-colors duration-200">{{ __('Profile Settings') }}</a></li>
                    <li><a href="{{ route('shop.account.orders') }}" class="hover:text-white transition-colors duration-200">{{ __('Order History') }}</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors duration-200">{{ __('Sign In') }}</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white transition-colors duration-200">{{ __('Register Account') }}</a></li>
                @endauth
            </ul>
        </div>

        {{-- Categories --}}
        <div>
            <h5 class="text-white font-bold uppercase tracking-wider text-xs mb-5">{{ __('Popular Categories') }}</h5>
            <ul class="space-y-3 text-sm">
                @forelse ($footerCategories as $cat)
                    <li><a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="hover:text-white transition-colors duration-200">{{ $cat->name }}</a></li>
                @empty
                    <li><a href="{{ route('shop.index') }}" class="hover:text-white transition-colors duration-200">{{ __('All Products') }}</a></li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Contact Row --}}
    <div class="border-t border-neutral-800 bg-neutral-950/50">
        <div class="shop-container py-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6 text-sm">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-8">
                @if(!empty($address['address']))
                    <span class="flex items-center gap-2.5 text-neutral-400">
                        <i class="ph ph-map-pin text-[color:var(--color-brand)] text-lg"></i>
                        <span>{{ $address['address'] }}</span>
                    </span>
                @endif
                <span class="flex items-center gap-2.5 text-neutral-400">
                    <i class="ph ph-phone text-[color:var(--color-brand)] text-lg"></i>
                    <a href="tel:{{ $company['phone'] }}" class="hover:text-white transition-colors duration-200">{{ $company['phone'] }}</a>
                </span>
                <span class="flex items-center gap-2.5 text-neutral-400">
                    <i class="ph ph-envelope text-[color:var(--color-brand)] text-lg"></i>
                    <a href="mailto:{{ $company['email'] }}" class="hover:text-white transition-colors duration-200">{{ $company['email'] }}</a>
                </span>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-2 bg-neutral-800/80 px-3 py-1.5 rounded-lg text-xs font-semibold text-neutral-300">
                    <i class="ph ph-truck text-sm text-[color:var(--color-brand)]"></i>
                    {{ __('Cash on Delivery') }}
                </span>
                <span class="inline-flex items-center gap-2 bg-neutral-800/80 px-3 py-1.5 rounded-lg text-xs font-semibold text-neutral-300">
                    <i class="ph ph-shield-check text-sm text-[color:var(--color-brand)]"></i>
                    {{ __('100% Secure Checkout') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Bottom Bar --}}
    <div class="border-t border-neutral-800/50 bg-neutral-950">
        <div class="shop-container py-5 text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-neutral-500">
            <span>&copy; {{ date('Y') }} {{ $company['name'] }}. {{ __('All rights reserved.') }}</span>
            <span class="text-neutral-600">{{ __('Designed for the ultimate shopping experience') }}</span>
        </div>
    </div>
</footer>
