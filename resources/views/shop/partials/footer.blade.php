@php
    $company          = config('application_info.company_info');
    $address          = config('application_info.address');
    $footerCategories = \App\Models\Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(6)->get();
    $fbLink           = collect(config('application_info.social_medias', []))->firstWhere('name', 'Facebook')['link'] ?? '#';
    $waNumber         = preg_replace('/\D/', '', $company['phone'] ?? '');
@endphp

{{-- ── Newsletter / CTA band (sits above the dark footer) ──── --}}
<section class="shop-container -mb-px relative z-10">
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-brand-dark via-brand to-brand-light shadow-2xl shadow-brand/20 px-6 sm:px-10 py-9 sm:py-11 translate-y-10">
        {{-- soft glow blobs --}}
        <div class="absolute -top-16 -right-10 w-56 h-56 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-10 w-64 h-64 rounded-full bg-black/10 blur-2xl pointer-events-none"></div>

        <div class="relative flex flex-col md:flex-row items-center justify-between gap-7">
            <div class="text-center md:text-left max-w-md">
                <div class="inline-flex items-center gap-2 mb-2.5 px-3 py-1 rounded-full bg-white/15 border border-white/20 backdrop-blur-sm">
                    <i class="ph ph-bell-ringing text-white text-sm"></i>
                    <span class="text-white text-[10px] font-bold uppercase tracking-[0.18em]">{{ __('Newsletter') }}</span>
                </div>
                <h4 class="text-white font-black text-2xl sm:text-3xl leading-tight mb-1.5 tracking-tight">
                    {{ __('Get Exclusive Deals') }}
                </h4>
                <p class="text-white/75 text-sm leading-relaxed">{{ __('New arrivals, flash sales & style tips — straight to your inbox.') }}</p>
            </div>
            <form class="flex w-full md:w-auto gap-2 shrink-0 max-w-md" onsubmit="return false;">
                <div class="relative flex-1 md:w-64">
                    <i class="ph ph-envelope-simple absolute left-3.5 top-1/2 -translate-y-1/2 text-white/60 text-base"></i>
                    <input type="email" placeholder="{{ __('Enter your email') }}"
                        class="w-full bg-white/15 border border-white/30 rounded-xl pl-10 pr-4 py-3.5 text-sm text-white placeholder:text-white/55 focus:outline-none focus:bg-white/25 focus:border-white/60 transition-all backdrop-blur-sm">
                </div>
                <button type="submit"
                    class="bg-white hover:bg-neutral-100 active:scale-95 text-brand font-extrabold text-xs uppercase tracking-wider px-6 py-3.5 rounded-xl transition-all whitespace-nowrap shadow-lg">
                    {{ __('Subscribe') }}
                </button>
            </form>
        </div>
    </div>
</section>

<footer class="shop-footer text-neutral-400">

    {{-- ── Trust strip ─────────────────────────────────────── --}}
    <div class="border-b border-white/[0.06] pt-20 sm:pt-24">
        <div class="shop-container pb-7">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-4 gap-y-5">
                @foreach ([
                    ['icon' => 'ph-truck',                   'title' => __('Free Delivery'),    'sub' => __('On orders above ৳5000')],
                    ['icon' => 'ph-hand-coins',              'title' => __('Cash on Delivery'), 'sub' => __('Pay when order arrives')],
                    ['icon' => 'ph-arrow-counter-clockwise', 'title' => __('Easy Returns'),     'sub' => __('7-day return policy')],
                    ['icon' => 'ph-shield-check',            'title' => __('100% Genuine'),     'sub' => __('Authentic products only')],
                ] as $t)
                    <div class="flex items-center gap-3 group">
                        <div class="w-11 h-11 rounded-2xl bg-white/[0.04] border border-white/[0.08] flex items-center justify-center shrink-0 group-hover:bg-brand group-hover:border-brand group-hover:scale-105 transition-all duration-300">
                            <i class="ph {{ $t['icon'] }} text-brand-light group-hover:text-white text-xl transition-colors duration-300"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-white text-xs font-bold leading-tight">{{ $t['title'] }}</p>
                            <p class="text-neutral-500 text-[11px] mt-0.5 leading-snug">{{ $t['sub'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Main columns ────────────────────────────────────── --}}
    <div class="shop-container pt-14 pb-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-6">

        {{-- Brand --}}
        <div class="sm:col-span-2 lg:col-span-4 space-y-6">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-brand flex items-center justify-center font-black text-white text-xl shadow-lg shadow-brand/40 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                    {{ strtoupper(substr($company['name'], 0, 1)) }}
                </div>
                <span class="text-2xl font-black text-white tracking-tight group-hover:text-brand-light transition-colors">
                    {{ $company['name'] }}
                </span>
            </a>

            <p class="text-sm leading-relaxed text-neutral-400 max-w-xs">
                {{ $company['description'] ?? __('Your trusted destination for quality products, delivered to your doorstep with care.') }}
            </p>

            {{-- Social --}}
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-neutral-500 mb-3">{{ __('Follow Us') }}</p>
                <div class="flex items-center gap-2.5">
                    <a href="{{ $fbLink }}" target="_blank" rel="noopener" aria-label="Facebook"
                       class="w-10 h-10 rounded-xl bg-[#1877F2]/10 hover:bg-[#1877F2] border border-[#1877F2]/20 hover:border-[#1877F2] flex items-center justify-center text-[#3b82f6] hover:text-white hover:-translate-y-0.5 transition-all duration-200">
                        <i class="ph ph-facebook-logo text-base"></i>
                    </a>
                    <a href="#" target="_blank" rel="noopener" aria-label="Instagram"
                       class="w-10 h-10 rounded-xl bg-pink-500/10 hover:bg-gradient-to-br hover:from-purple-600 hover:to-pink-500 border border-pink-500/20 hover:border-pink-500 flex items-center justify-center text-pink-400 hover:text-white hover:-translate-y-0.5 transition-all duration-200">
                        <i class="ph ph-instagram-logo text-base"></i>
                    </a>
                    <a href="#" target="_blank" rel="noopener" aria-label="YouTube"
                       class="w-10 h-10 rounded-xl bg-red-600/10 hover:bg-red-600 border border-red-600/20 hover:border-red-600 flex items-center justify-center text-red-400 hover:text-white hover:-translate-y-0.5 transition-all duration-200">
                        <i class="ph ph-youtube-logo text-base"></i>
                    </a>
                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener" aria-label="WhatsApp"
                       class="w-10 h-10 rounded-xl bg-emerald-500/10 hover:bg-emerald-500 border border-emerald-500/20 hover:border-emerald-500 flex items-center justify-center text-emerald-400 hover:text-white hover:-translate-y-0.5 transition-all duration-200">
                        <i class="ph ph-whatsapp-logo text-base"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="lg:col-span-2 lg:col-start-6">
            <h5 class="foot-heading">{{ __('Quick Links') }}</h5>
            <ul class="space-y-3">
                @foreach ([
                    [route('home'),            __('Home')],
                    [route('shop.index'),      __('All Products')],
                    [route('shop.cart.index'), __('Shopping Cart')],
                    [route('shop.checkout'),   __('Checkout')],
                ] as [$url, $label])
                    <li><a href="{{ $url }}" class="foot-link"><i class="ph ph-caret-right"></i>{{ $label }}</a></li>
                @endforeach
            </ul>
        </div>

        {{-- My Account --}}
        <div class="lg:col-span-2">
            <h5 class="foot-heading">{{ __('My Account') }}</h5>
            <ul class="space-y-3">
                @auth
                    <li><a href="{{ route('shop.account.index') }}" class="foot-link"><i class="ph ph-caret-right"></i>{{ __('My Profile') }}</a></li>
                    <li><a href="{{ route('shop.account.orders') }}" class="foot-link"><i class="ph ph-caret-right"></i>{{ __('Order History') }}</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="foot-link"><i class="ph ph-caret-right"></i>{{ __('Sign In') }}</a></li>
                    <li><a href="{{ route('register') }}" class="foot-link"><i class="ph ph-caret-right"></i>{{ __('Create Account') }}</a></li>
                @endauth
            </ul>
        </div>

        {{-- Categories --}}
        <div class="lg:col-span-2">
            <h5 class="foot-heading">{{ __('Categories') }}</h5>
            <ul class="space-y-3">
                @forelse ($footerCategories as $cat)
                    <li><a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="foot-link"><i class="ph ph-caret-right"></i>{{ $cat->name }}</a></li>
                @empty
                    <li><a href="{{ route('shop.index') }}" class="foot-link"><i class="ph ph-caret-right"></i>{{ __('All Products') }}</a></li>
                @endforelse
            </ul>
        </div>

        {{-- Contact --}}
        <div class="sm:col-span-2 lg:col-span-2">
            <h5 class="foot-heading">{{ __('Contact Us') }}</h5>
            <ul class="space-y-4">
                @if (!empty($address['address']))
                    <li class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-brand/10 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="ph ph-map-pin text-brand-light text-xs"></i>
                        </div>
                        <span class="text-sm text-neutral-400 leading-snug">{{ $address['address'] }}</span>
                    </li>
                @endif
                @if (!empty($company['phone']))
                    <li>
                        <a href="tel:{{ $company['phone'] }}" class="flex items-center gap-3 text-sm text-neutral-400 hover:text-white transition-colors group">
                            <div class="w-7 h-7 rounded-lg bg-brand/10 group-hover:bg-brand flex items-center justify-center shrink-0 transition-colors">
                                <i class="ph ph-phone text-brand-light group-hover:text-white text-xs transition-colors"></i>
                            </div>
                            {{ $company['phone'] }}
                        </a>
                    </li>
                @endif
                @if (!empty($company['email']))
                    <li>
                        <a href="mailto:{{ $company['email'] }}" class="flex items-center gap-3 text-sm text-neutral-400 hover:text-white transition-colors group">
                            <div class="w-7 h-7 rounded-lg bg-brand/10 group-hover:bg-brand flex items-center justify-center shrink-0 transition-colors">
                                <i class="ph ph-envelope text-brand-light group-hover:text-white text-xs transition-colors"></i>
                            </div>
                            <span class="break-all">{{ $company['email'] }}</span>
                        </a>
                    </li>
                @endif
                @if ($waNumber)
                    <li class="pt-1">
                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener"
                           class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-lg shadow-emerald-900/30 w-full sm:w-auto">
                            <i class="ph ph-whatsapp-logo text-sm"></i>
                            {{ __('Chat on WhatsApp') }}
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    {{-- ── Payment / delivery badges ───────────────────────── --}}
    <div class="shop-container">
        <div class="border-t border-white/[0.06] py-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-neutral-500">{{ __('We Accept') }}</p>
            <div class="flex flex-wrap items-center justify-center gap-2.5">
                @foreach ([
                    ['icon' => 'ph-money',     'label' => __('Cash')],
                    ['icon' => 'ph-wallet',    'label' => 'bKash'],
                    ['icon' => 'ph-device-mobile', 'label' => 'Nagad'],
                    ['icon' => 'ph-credit-card', 'label' => __('Card')],
                ] as $p)
                    <span class="inline-flex items-center gap-1.5 bg-white/[0.04] border border-white/[0.08] text-neutral-300 text-[11px] font-semibold px-3 py-1.5 rounded-lg">
                        <i class="ph {{ $p['icon'] }} text-brand-light text-sm"></i>{{ $p['label'] }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Bottom bar ──────────────────────────────────────── --}}
    <div class="border-t border-white/[0.06] bg-black/25">
        <div class="shop-container py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-neutral-500 text-center sm:text-left">
                &copy; {{ date('Y') }}
                <a href="{{ route('home') }}" class="text-neutral-300 font-semibold hover:text-brand-light transition-colors">{{ $company['name'] }}</a>.
                {{ __('All rights reserved.') }}
            </p>
            <div class="flex items-center gap-4 text-[11px] text-neutral-600">
                <span class="flex items-center gap-1.5"><i class="ph ph-lock-simple text-brand/60 text-xs"></i>{{ __('Secure Checkout') }}</span>
                <span class="w-px h-3 bg-white/10"></span>
                <span class="flex items-center gap-1.5"><i class="ph ph-headset text-brand/60 text-xs"></i>{{ __('24/7 Support') }}</span>
            </div>
        </div>
    </div>
</footer>

{{-- ── Back to top ─────────────────────────────────────────── --}}
<button type="button" id="back-to-top" class="back-to-top" aria-label="{{ __('Back to top') }}">
    <i class="ph ph-arrow-up text-lg"></i>
</button>

@push('scripts')
<script>
    (function () {
        const btn = document.getElementById('back-to-top');
        if (!btn) return;
        const toggle = () => btn.classList.toggle('show', window.scrollY > 400);
        window.addEventListener('scroll', toggle, { passive: true });
        btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
        toggle();
    })();
</script>
@endpush
