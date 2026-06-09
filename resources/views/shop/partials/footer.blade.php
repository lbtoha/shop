@php
    $company = config('application_info.company_info');
    $address = config('application_info.address');
    $footerCategories = \App\Models\Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(5)->get();
@endphp

{{-- Newsletter strip --}}
<section class="bg-[color:var(--color-brand-soft)]">
    <div class="shop-container py-9 flex flex-col md:flex-row items-center justify-between gap-5">
        <div class="text-center md:text-left">
            <h3 class="text-xl font-bold text-[color:var(--color-ink)]">{{ __('Subscribe to our Newsletter') }}</h3>
            <p class="text-sm text-[color:var(--color-muted)] mt-1">{{ __('Get the latest deals and new arrivals straight to your inbox') }}</p>
        </div>
        <form class="flex w-full md:w-auto max-w-md" onsubmit="return false;">
            <input type="email" placeholder="{{ __('Your email address') }}"
                class="flex-1 md:w-80 border border-[color:var(--color-line)] rounded-l-full py-3 px-5 text-sm focus:outline-none bg-white">
            <button class="bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white font-medium px-6 rounded-r-full text-sm">{{ __('Subscribe') }}</button>
        </form>
    </div>
</section>

<footer class="bg-[color:var(--color-ink)] text-neutral-400">
    <div class="shop-container py-14 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">
        {{-- Brand + app --}}
        <div class="col-span-2 lg:col-span-2">
            <h3 class="text-white text-2xl font-extrabold mb-3">{{ $company['name'] }}</h3>
            <p class="text-sm leading-relaxed max-w-sm">{{ $company['description'] }}</p>
            <p class="text-white font-medium mt-5 mb-2 text-sm">{{ __('Download Our App') }}</p>
            <div class="flex items-center gap-3">
                <a href="#" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/15 rounded-lg px-3 py-2 text-white text-sm">
                    <i class="ph ph-google-play-logo text-xl"></i> {{ __('Google Play') }}
                </a>
                <a href="#" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/15 rounded-lg px-3 py-2 text-white text-sm">
                    <i class="ph ph-apple-logo text-xl"></i> {{ __('App Store') }}
                </a>
            </div>
        </div>

        <div>
            <h5 class="text-white font-semibold mb-4">{{ __('About') }}</h5>
            <ul class="space-y-2.5 text-sm">
                <li><a href="{{ route('home') }}" class="hover:text-[color:var(--color-brand-light)]">{{ __('Home') }}</a></li>
                <li><a href="{{ route('shop.index') }}" class="hover:text-[color:var(--color-brand-light)]">{{ __('Shop') }}</a></li>
                <li><a href="{{ route('shop.cart.index') }}" class="hover:text-[color:var(--color-brand-light)]">{{ __('Cart') }}</a></li>
            </ul>
        </div>

        <div>
            <h5 class="text-white font-semibold mb-4">{{ __('My Account') }}</h5>
            <ul class="space-y-2.5 text-sm">
                @auth
                    <li><a href="{{ route('shop.account.index') }}" class="hover:text-[color:var(--color-brand-light)]">{{ __('Your Account') }}</a></li>
                    <li><a href="{{ route('shop.account.orders') }}" class="hover:text-[color:var(--color-brand-light)]">{{ __('My Orders') }}</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="hover:text-[color:var(--color-brand-light)]">{{ __('Login') }}</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-[color:var(--color-brand-light)]">{{ __('Register') }}</a></li>
                @endauth
            </ul>
        </div>

        <div>
            <h5 class="text-white font-semibold mb-4">{{ __('Categories') }}</h5>
            <ul class="space-y-2.5 text-sm">
                @forelse ($footerCategories as $cat)
                    <li><a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="hover:text-[color:var(--color-brand-light)]">{{ $cat->name }}</a></li>
                @empty
                    <li><a href="{{ route('shop.index') }}" class="hover:text-[color:var(--color-brand-light)]">{{ __('All Products') }}</a></li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Contact row --}}
    <div class="border-t border-white/10">
        <div class="shop-container py-5 flex flex-col md:flex-row md:items-center justify-between gap-4 text-sm">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6">
                <span class="flex items-center gap-2"><i class="ph ph-map-pin"></i> {{ $address['address'] ?? '' }}</span>
                <span class="flex items-center gap-2"><i class="ph ph-phone"></i> <a href="tel:{{ $company['phone'] }}" class="hover:text-[color:var(--color-brand-light)]">{{ $company['phone'] }}</a></span>
                <span class="flex items-center gap-2"><i class="ph ph-envelope"></i> <a href="mailto:{{ $company['email'] }}" class="hover:text-[color:var(--color-brand-light)]">{{ $company['email'] }}</a></span>
            </div>
            <div class="flex items-center gap-2">
                @foreach (['ph-facebook-logo', 'ph-instagram-logo', 'ph-twitter-logo', 'ph-youtube-logo'] as $icon)
                    <a href="#" class="w-9 h-9 rounded-full bg-white/10 hover:bg-[color:var(--color-brand)] flex items-center justify-center text-white transition"><i class="ph {{ $icon }}"></i></a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Copyright --}}
    <div class="border-t border-white/10">
        <div class="shop-container py-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-neutral-500">
            <span>&copy; {{ date('Y') }} {{ $company['name'] }}. {{ __('All rights reserved.') }}</span>
            <span class="inline-flex items-center gap-1 bg-white/10 px-2.5 py-1 rounded text-neutral-300"><i class="ph ph-money"></i> {{ __('Cash on Delivery') }}</span>
        </div>
    </div>
</footer>
