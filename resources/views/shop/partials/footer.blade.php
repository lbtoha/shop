@php
    $company = config('application_info.company_info');
    $address = config('application_info.address');
@endphp

{{-- Newsletter strip --}}
<section class="bg-[color:var(--color-brand-soft)]">
    <div class="max-w-7xl mx-auto px-4 py-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="text-center md:text-left">
            <h3 class="text-lg font-bold text-[color:var(--color-ink)]">{{ __('Subscribe to our newsletter') }}</h3>
            <p class="text-sm text-[color:var(--color-muted)]">{{ __('Get the latest deals and new arrivals in your inbox') }}</p>
        </div>
        <form class="flex w-full md:w-auto max-w-md" onsubmit="return false;">
            <input type="email" placeholder="{{ __('Your email address') }}"
                class="flex-1 md:w-72 border border-[color:var(--color-line)] rounded-l-md py-2.5 px-4 text-sm focus:outline-none bg-white">
            <button class="bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white font-medium px-5 rounded-r-md text-sm">{{ __('Subscribe') }}</button>
        </form>
    </div>
</section>

<footer class="bg-[color:var(--color-ink)] text-neutral-400">
    <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-2 md:grid-cols-4 gap-8">
        <div class="col-span-2 md:col-span-1">
            <h3 class="text-white text-xl font-extrabold mb-3">{{ $company['name'] }}</h3>
            <p class="text-sm leading-relaxed">{{ $company['description'] }}</p>
            <div class="flex items-center gap-2 mt-4">
                @foreach (['ph-facebook-logo', 'ph-instagram-logo', 'ph-twitter-logo', 'ph-youtube-logo'] as $icon)
                    <a href="#" class="w-9 h-9 rounded-full bg-white/10 hover:bg-[color:var(--color-brand)] flex items-center justify-center text-white transition">
                        <i class="ph {{ $icon }}"></i>
                    </a>
                @endforeach
            </div>
        </div>

        <div>
            <h4 class="text-white font-semibold mb-4">{{ __('Shop') }}</h4>
            <ul class="space-y-2.5 text-sm">
                <li><a href="{{ route('home') }}" class="hover:text-[color:var(--color-brand-light)]">{{ __('Home') }}</a></li>
                <li><a href="{{ route('shop.index') }}" class="hover:text-[color:var(--color-brand-light)]">{{ __('All Products') }}</a></li>
                <li><a href="{{ route('shop.cart.index') }}" class="hover:text-[color:var(--color-brand-light)]">{{ __('Cart') }}</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-white font-semibold mb-4">{{ __('My Account') }}</h4>
            <ul class="space-y-2.5 text-sm">
                @auth
                    <li><a href="{{ route('shop.account.index') }}" class="hover:text-[color:var(--color-brand-light)]">{{ __('Dashboard') }}</a></li>
                    <li><a href="{{ route('shop.account.orders') }}" class="hover:text-[color:var(--color-brand-light)]">{{ __('My Orders') }}</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="hover:text-[color:var(--color-brand-light)]">{{ __('Login') }}</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-[color:var(--color-brand-light)]">{{ __('Register') }}</a></li>
                @endauth
            </ul>
        </div>

        <div>
            <h4 class="text-white font-semibold mb-4">{{ __('Contact') }}</h4>
            <ul class="space-y-2.5 text-sm">
                <li class="flex items-start gap-2"><i class="ph ph-map-pin mt-0.5"></i> {{ $address['address'] ?? '' }}</li>
                <li class="flex items-center gap-2"><i class="ph ph-phone"></i> <a href="tel:{{ $company['phone'] }}" class="hover:text-[color:var(--color-brand-light)]">{{ $company['phone'] }}</a></li>
                <li class="flex items-center gap-2"><i class="ph ph-envelope"></i> <a href="mailto:{{ $company['email'] }}" class="hover:text-[color:var(--color-brand-light)]">{{ $company['email'] }}</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-neutral-500">
            <span>&copy; {{ date('Y') }} {{ $company['name'] }}. {{ __('All rights reserved.') }}</span>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1 bg-white/10 px-2.5 py-1 rounded text-neutral-300"><i class="ph ph-money"></i> {{ __('Cash on Delivery') }}</span>
            </div>
        </div>
    </div>
</footer>
