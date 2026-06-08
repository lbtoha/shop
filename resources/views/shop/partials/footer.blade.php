@php
    $company = config('application_info.company_info');
    $address = config('application_info.address');
@endphp

<footer class="bg-[color:var(--color-ink)] text-neutral-300 mt-16">
    <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
            <h3 class="text-white text-lg font-bold mb-3">{{ $company['name'] }}</h3>
            <p class="text-sm leading-relaxed text-neutral-400">{{ $company['description'] }}</p>
        </div>

        <div>
            <h4 class="text-white font-semibold mb-3">{{ __('Quick Links') }}</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a></li>
                <li><a href="{{ route('shop.index') }}" class="hover:text-white">{{ __('Shop') }}</a></li>
                <li><a href="{{ route('shop.cart.index') }}" class="hover:text-white">{{ __('Cart') }}</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-white font-semibold mb-3">{{ __('Contact') }}</h4>
            <ul class="space-y-2 text-sm text-neutral-400">
                <li class="flex items-start gap-2"><i class="ph ph-map-pin mt-0.5"></i> {{ $address['address'] ?? '' }}</li>
                <li class="flex items-center gap-2"><i class="ph ph-phone"></i> <a href="tel:{{ $company['phone'] }}" class="hover:text-white">{{ $company['phone'] }}</a></li>
                <li class="flex items-center gap-2"><i class="ph ph-envelope"></i> <a href="mailto:{{ $company['email'] }}" class="hover:text-white">{{ $company['email'] }}</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-white font-semibold mb-3">{{ __('Payment') }}</h4>
            <p class="text-sm text-neutral-400">{{ __('We accept Cash on Delivery.') }}</p>
            <div class="mt-3 inline-flex items-center gap-2 bg-white/10 px-3 py-2 rounded text-sm">
                <i class="ph ph-money"></i> {{ __('Cash on Delivery') }}
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 py-4 text-center text-xs text-neutral-500">
            &copy; {{ date('Y') }} {{ $company['name'] }}. {{ __('All rights reserved.') }}
        </div>
    </div>
</footer>
