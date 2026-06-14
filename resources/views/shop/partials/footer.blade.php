@php
    $company         = config('application_info.company_info');
    $address         = config('application_info.address');
    $footerCategories = \App\Models\Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(5)->get();
    $fbLink = collect(config('application_info.social_medias', []))->firstWhere('name', 'Facebook')['link'] ?? '#';
@endphp

<footer class="bg-[#0f0f1a] text-neutral-400">

    {{-- ── Newsletter bar ──────────────────────────────────── --}}
    <div class="border-b border-white/5">
        <div class="shop-container py-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div>
                <h4 class="text-white font-bold text-base mb-1">{{ __('Stay in the loop') }}</h4>
                <p class="text-sm text-neutral-500">{{ __('Get new arrivals, exclusive deals, and style tips straight to your inbox.') }}</p>
            </div>
            <form class="flex w-full sm:w-auto gap-2 shrink-0" onsubmit="return false;">
                <input type="email" placeholder="{{ __('Your email address') }}"
                    class="flex-1 sm:w-64 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder:text-neutral-500 focus:outline-none focus:border-brand/60 focus:ring-2 focus:ring-brand/20 transition-all">
                <button type="submit"
                    class="bg-brand hover:bg-brand-dark text-white text-xs font-bold uppercase tracking-wider px-5 py-2.5 rounded-xl transition-colors whitespace-nowrap shadow-lg shadow-brand/20">
                    {{ __('Subscribe') }}
                </button>
            </form>
        </div>
    </div>

    {{-- ── Main footer columns ──────────────────────────────── --}}
    <div class="shop-container py-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8">

        {{-- Brand --}}
        <div class="sm:col-span-2 lg:col-span-4 space-y-5">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 group">
                <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-brand text-white font-black text-lg shadow-lg shadow-brand/30">
                    {{ strtoupper(substr($company['name'], 0, 1)) }}
                </span>
                <span class="text-xl font-extrabold text-white tracking-tight group-hover:text-brand-light transition-colors">
                    {{ $company['name'] }}
                </span>
            </a>
            <p class="text-sm leading-relaxed text-neutral-400 max-w-xs">
                {{ $company['description'] ?? __('Your trusted destination for quality products, delivered to your doorstep with care.') }}
            </p>

            {{-- Trust badges --}}
            <div class="flex flex-wrap gap-2 pt-1">
                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-neutral-300 bg-white/5 border border-white/8 px-3 py-1.5 rounded-lg">
                    <i class="ph ph-truck text-brand-light text-sm"></i>{{ __('Cash on Delivery') }}
                </span>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-neutral-300 bg-white/5 border border-white/8 px-3 py-1.5 rounded-lg">
                    <i class="ph ph-shield-check text-brand-light text-sm"></i>{{ __('100% Secure') }}
                </span>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-neutral-300 bg-white/5 border border-white/8 px-3 py-1.5 rounded-lg">
                    <i class="ph ph-arrow-counter-clockwise text-brand-light text-sm"></i>{{ __('7-day Returns') }}
                </span>
            </div>

            {{-- Social --}}
            <div class="flex items-center gap-2.5 pt-1">
                @foreach ([
                    ['icon' => 'ph-facebook-logo',  'url' => $fbLink,  'label' => 'Facebook'],
                    ['icon' => 'ph-instagram-logo',  'url' => '#',      'label' => 'Instagram'],
                    ['icon' => 'ph-youtube-logo',    'url' => '#',      'label' => 'YouTube'],
                    ['icon' => 'ph-whatsapp-logo',   'url' => 'https://wa.me/' . preg_replace('/\D/', '', $company['phone'] ?? ''), 'label' => 'WhatsApp'],
                ] as $s)
                    <a href="{{ $s['url'] }}" target="_blank" rel="noopener" aria-label="{{ $s['label'] }}"
                       class="w-8 h-8 rounded-xl bg-white/6 hover:bg-brand border border-white/8 hover:border-brand flex items-center justify-center text-neutral-400 hover:text-white transition-all duration-200">
                        <i class="ph {{ $s['icon'] }} text-sm"></i>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Quick links --}}
        <div class="lg:col-span-2 lg:col-start-6">
            <h5 class="text-white font-bold text-xs uppercase tracking-[0.12em] mb-5">{{ __('Quick Links') }}</h5>
            <ul class="space-y-3 text-sm">
                <li><a href="{{ route('home') }}"
                       class="flex items-center gap-2 text-neutral-400 hover:text-white transition-colors group">
                    <i class="ph ph-caret-right text-brand/50 text-xs group-hover:text-brand transition-colors"></i>{{ __('Home') }}
                </a></li>
                <li><a href="{{ route('shop.index') }}"
                       class="flex items-center gap-2 text-neutral-400 hover:text-white transition-colors group">
                    <i class="ph ph-caret-right text-brand/50 text-xs group-hover:text-brand transition-colors"></i>{{ __('All Products') }}
                </a></li>
                <li><a href="{{ route('shop.cart.index') }}"
                       class="flex items-center gap-2 text-neutral-400 hover:text-white transition-colors group">
                    <i class="ph ph-caret-right text-brand/50 text-xs group-hover:text-brand transition-colors"></i>{{ __('Shopping Cart') }}
                </a></li>
                <li><a href="{{ route('shop.checkout') }}"
                       class="flex items-center gap-2 text-neutral-400 hover:text-white transition-colors group">
                    <i class="ph ph-caret-right text-brand/50 text-xs group-hover:text-brand transition-colors"></i>{{ __('Checkout') }}
                </a></li>
            </ul>
        </div>

        {{-- Account --}}
        <div class="lg:col-span-2">
            <h5 class="text-white font-bold text-xs uppercase tracking-[0.12em] mb-5">{{ __('My Account') }}</h5>
            <ul class="space-y-3 text-sm">
                @auth
                    <li><a href="{{ route('shop.account.index') }}"
                           class="flex items-center gap-2 text-neutral-400 hover:text-white transition-colors group">
                        <i class="ph ph-caret-right text-brand/50 text-xs group-hover:text-brand transition-colors"></i>{{ __('Profile') }}
                    </a></li>
                    <li><a href="{{ route('shop.account.orders') }}"
                           class="flex items-center gap-2 text-neutral-400 hover:text-white transition-colors group">
                        <i class="ph ph-caret-right text-brand/50 text-xs group-hover:text-brand transition-colors"></i>{{ __('Order History') }}
                    </a></li>
                @else
                    <li><a href="{{ route('login') }}"
                           class="flex items-center gap-2 text-neutral-400 hover:text-white transition-colors group">
                        <i class="ph ph-caret-right text-brand/50 text-xs group-hover:text-brand transition-colors"></i>{{ __('Sign In') }}
                    </a></li>
                    <li><a href="{{ route('register') }}"
                           class="flex items-center gap-2 text-neutral-400 hover:text-white transition-colors group">
                        <i class="ph ph-caret-right text-brand/50 text-xs group-hover:text-brand transition-colors"></i>{{ __('Create Account') }}
                    </a></li>
                @endauth
            </ul>
        </div>

        {{-- Categories --}}
        <div class="lg:col-span-2">
            <h5 class="text-white font-bold text-xs uppercase tracking-[0.12em] mb-5">{{ __('Categories') }}</h5>
            <ul class="space-y-3 text-sm">
                @forelse ($footerCategories as $cat)
                    <li><a href="{{ route('shop.index', ['category' => $cat->slug]) }}"
                           class="flex items-center gap-2 text-neutral-400 hover:text-white transition-colors group">
                        <i class="ph ph-caret-right text-brand/50 text-xs group-hover:text-brand transition-colors"></i>{{ $cat->name }}
                    </a></li>
                @empty
                    <li><a href="{{ route('shop.index') }}"
                           class="flex items-center gap-2 text-neutral-400 hover:text-white transition-colors group">
                        <i class="ph ph-caret-right text-brand/50 text-xs group-hover:text-brand transition-colors"></i>{{ __('All Products') }}
                    </a></li>
                @endforelse
            </ul>
        </div>

        {{-- Contact --}}
        <div class="sm:col-span-2 lg:col-span-2">
            <h5 class="text-white font-bold text-xs uppercase tracking-[0.12em] mb-5">{{ __('Contact Us') }}</h5>
            <ul class="space-y-3.5 text-sm">
                @if (!empty($address['address']))
                    <li class="flex items-start gap-2.5 text-neutral-400">
                        <i class="ph ph-map-pin text-brand-light text-base mt-0.5 shrink-0"></i>
                        <span>{{ $address['address'] }}</span>
                    </li>
                @endif
                <li>
                    <a href="tel:{{ $company['phone'] }}"
                       class="flex items-center gap-2.5 text-neutral-400 hover:text-white transition-colors">
                        <i class="ph ph-phone text-brand-light text-base shrink-0"></i>
                        <span>{{ $company['phone'] }}</span>
                    </a>
                </li>
                <li>
                    <a href="mailto:{{ $company['email'] }}"
                       class="flex items-center gap-2.5 text-neutral-400 hover:text-white transition-colors">
                        <i class="ph ph-envelope text-brand-light text-base shrink-0"></i>
                        <span class="break-all">{{ $company['email'] }}</span>
                    </a>
                </li>
                <li class="pt-1">
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $company['phone'] ?? '') }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition-colors shadow-lg shadow-emerald-900/30">
                        <i class="ph ph-whatsapp-logo text-sm"></i>{{ __('WhatsApp Chat') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- ── Bottom bar ───────────────────────────────────────── --}}
    <div class="border-t border-white/5 bg-black/20">
        <div class="shop-container py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-neutral-500">
            <span>&copy; {{ date('Y') }} <span class="text-neutral-400 font-medium">{{ $company['name'] }}</span>. {{ __('All rights reserved.') }}</span>
            <span class="text-neutral-600">{{ __('Designed for the ultimate shopping experience') }}</span>
        </div>
    </div>

</footer>
