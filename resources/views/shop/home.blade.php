@extends('shop.layouts.app')

@section('title', config('application_info.company_info.name') . ' — ' . __('Premium Fashion & Lifestyle'))

@section('content')

{{-- ── Hero Carousel ─────────────────────────────────────────── --}}
@if ($banners->isNotEmpty())
<div class="shop-container mt-5 sm:mt-6">
    <div class="relative w-full h-[280px] sm:h-[420px] lg:h-[520px] rounded-2xl sm:rounded-3xl overflow-hidden group/hero bg-ink"
         data-hero>

        {{-- Slides --}}
        @foreach ($banners as $i => $banner)
            <div data-slide
                 class="{{ $i === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 hidden' }} absolute inset-0 transition-all duration-1000 ease-in-out">
                <div class="absolute inset-0 overflow-hidden">
                    <img src="{{ $banner->image }}" alt="{{ $banner->title }}"
                         class="w-full h-full object-cover">
                </div>
                {{-- Layered gradient for readability --}}
                <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/40 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>

                {{-- Slide content --}}
                <div class="absolute inset-0 flex items-center">
                    <div class="px-6 sm:px-12 lg:px-16 space-y-3 sm:space-y-5 max-w-xl content-anim">
                        {{-- Eyebrow badge --}}
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 backdrop-blur-sm text-white">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-light animate-pulse"></span>
                            <span class="text-[10px] sm:text-xs font-bold uppercase tracking-[0.15em]">{{ __('Special Offer') }}</span>
                        </span>

                        {{-- Title --}}
                        <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black uppercase tracking-tight leading-[1.1] text-white">
                            {!! str_replace(' & ', ' <span class="text-brand-light">&</span> ', e(__($banner->title))) !!}
                        </h1>

                        {{-- Subtitle --}}
                        <p class="text-xs sm:text-sm text-white/75 font-medium max-w-sm leading-relaxed">
                            {{ __($banner->subtitle) }}
                        </p>

                        {{-- CTA --}}
                        <div class="flex items-center gap-3 pt-1">
                            <a href="{{ $banner->link ?: route('shop.index') }}"
                               class="btn-brand text-xs sm:text-sm py-2.5 sm:py-3 px-5 sm:px-7 group/btn">
                                <span>{{ __($banner->button_text ?: 'Shop Now') }}</span>
                                <i class="ph ph-arrow-right transition-transform duration-200 group-hover/btn:translate-x-1"></i>
                            </a>
                            <a href="{{ route('shop.index') }}"
                               class="text-xs sm:text-sm text-white/70 hover:text-white font-semibold underline underline-offset-4 transition-colors">
                               {{ __('Browse All') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Controls --}}
        <button type="button" data-hero-prev
            class="absolute left-3 sm:left-5 top-1/2 -translate-y-1/2 z-20 w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-black/25 hover:bg-brand text-white flex items-center justify-center backdrop-blur-sm border border-white/15 hover:border-brand transition-all duration-200 opacity-0 group-hover/hero:opacity-100 shadow-lg cursor-pointer">
            <i class="ph ph-caret-left text-lg sm:text-xl"></i>
        </button>
        <button type="button" data-hero-next
            class="absolute right-3 sm:right-5 top-1/2 -translate-y-1/2 z-20 w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-black/25 hover:bg-brand text-white flex items-center justify-center backdrop-blur-sm border border-white/15 hover:border-brand transition-all duration-200 opacity-0 group-hover/hero:opacity-100 shadow-lg cursor-pointer">
            <i class="ph ph-caret-right text-lg sm:text-xl"></i>
        </button>

        {{-- Dots --}}
        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-20 flex gap-1.5">
            @foreach ($banners as $i => $banner)
                <button type="button" data-hero-dot="{{ $i }}"
                    class="w-3 h-1.5 rounded-full bg-white/35 hover:bg-white/60 transition-all duration-300 cursor-pointer"
                    aria-label="Slide {{ $i + 1 }}"></button>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="shop-container">

    {{-- ── Feature Strip ─────────────────────────────────────── --}}
    <section class="mt-6 sm:mt-8 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @php
            $features = [
                ['icon' => 'ph-truck',                   'title' => __('Free Delivery'),     'text' => __('Orders above ৳5000')],
                ['icon' => 'ph-arrow-counter-clockwise', 'title' => __('Easy Returns'),       'text' => __('7-day exchange policy')],
                ['icon' => 'ph-hand-coins',              'title' => __('Cash on Delivery'),   'text' => __('Pay at your doorstep')],
                ['icon' => 'ph-shield-check',            'title' => __('Safe Shopping'),      'text' => __('100% genuine products')],
            ];
        @endphp
        @foreach ($features as $f)
            <div class="feature-card">
                <div class="w-11 h-11 rounded-xl bg-brand text-white flex items-center justify-center shrink-0 shadow-md shadow-brand/20">
                    <i class="ph {{ $f['icon'] }} text-xl"></i>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs sm:text-sm font-bold text-ink leading-tight truncate">{{ $f['title'] }}</h4>
                    <p class="text-[11px] text-muted font-medium mt-0.5 leading-snug">{{ $f['text'] }}</p>
                </div>
            </div>
        @endforeach
    </section>

    {{-- ── Shop By Category ──────────────────────────────────── --}}
    @if ($categories->isNotEmpty())
        <section class="mt-14 sm:mt-16">
            <div class="section-heading"><h2>{{ __('Shop by Category') }}</h2></div>
            <div class="flex flex-wrap items-start justify-center gap-6 sm:gap-10 md:gap-14">
                @foreach ($categories as $category)
                    <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                       class="category-pill group">
                        <div class="cp-img">
                            <img src="{{ $category->image }}" alt="{{ $category->name }}"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        </div>
                        <span class="text-xs sm:text-[13px] font-bold text-ink uppercase tracking-wider text-center group-hover:text-brand transition-colors duration-200 line-clamp-2 leading-snug max-w-[90px] sm:max-w-[110px]">
                            {{ $category->name }}
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

</div>

{{-- ── Featured Products Slider ──────────────────────────────── --}}
@if ($featuredProducts->isNotEmpty())
    @include('shop.partials.product-slider-section', [
        'title'   => __('Featured Products'),
        'products' => $featuredProducts,
        'viewAll' => route('shop.index', ['featured' => 1]),
    ])
@endif

{{-- ── Women's Products Grid ─────────────────────────────────── --}}
@if ($ladiesThreePiece->isNotEmpty())
    @include('shop.partials.product-section', [
        'title'    => __("Women's Collection"),
        'products' => $ladiesThreePiece,
        'viewAll'  => route('shop.index', ['category' => 'womens-products']),
    ])
@endif

{{-- ── Connect / Support Section ─────────────────────────────── --}}
<section class="shop-container mt-14 sm:mt-16 mb-4">
    <div class="section-heading"><h2>{{ __('Connect with Us') }}</h2></div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">

        {{-- WhatsApp --}}
        <div class="group bg-white border border-neutral-100 hover:border-emerald-200 rounded-2xl p-6 sm:p-7 flex flex-col gap-5 transition-all duration-300 hover:shadow-xl hover:shadow-emerald-50/80">
            <div class="flex items-center justify-between">
                <span class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-sm">
                    <i class="ph ph-whatsapp-logo text-2xl"></i>
                </span>
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full uppercase tracking-wider">{{ __('Live') }}</span>
            </div>
            <div>
                <h4 class="font-extrabold text-ink text-base mb-2 group-hover:text-emerald-600 transition-colors">{{ __('Order via WhatsApp') }}</h4>
                <p class="text-neutral-500 text-sm leading-relaxed">{{ __('Chat directly with our team for orders, sizing help, and customization.') }}</p>
            </div>
            <a href="https://wa.me/{{ preg_replace('/\D/', '', config('application_info.company_info.phone') ?? '') }}"
               target="_blank" rel="noopener"
               class="mt-auto w-full flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs uppercase tracking-wider py-3 px-5 rounded-xl transition-colors shadow-md shadow-emerald-200">
                <i class="ph ph-chat-circle text-base"></i>
                {{ __('Start Chat') }}
            </a>
        </div>

        {{-- Facebook --}}
        @php
            $fbLink = collect(config('application_info.social_medias', []))->firstWhere('name', 'Facebook')['link'] ?? '#';
        @endphp
        <div class="group bg-white border border-neutral-100 hover:border-blue-200 rounded-2xl p-6 sm:p-7 flex flex-col gap-5 transition-all duration-300 hover:shadow-xl hover:shadow-blue-50/80">
            <div class="flex items-center justify-between">
                <span class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-sm">
                    <i class="ph ph-facebook-logo text-2xl"></i>
                </span>
                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full uppercase tracking-wider">{{ __('Follow') }}</span>
            </div>
            <div>
                <h4 class="font-extrabold text-ink text-base mb-2 group-hover:text-blue-600 transition-colors">{{ __('Facebook Page') }}</h4>
                <p class="text-neutral-500 text-sm leading-relaxed">{{ __('Daily arrivals, customer photos, live shows, and exclusive Facebook deals.') }}</p>
            </div>
            <a href="{{ $fbLink }}" target="_blank" rel="noopener"
               class="mt-auto w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider py-3 px-5 rounded-xl transition-colors shadow-md shadow-blue-200">
                <i class="ph ph-thumbs-up text-base"></i>
                {{ __('Visit Page') }}
            </a>
        </div>

        {{-- Call --}}
        <div class="group bg-white border border-neutral-100 hover:border-brand/30 rounded-2xl p-6 sm:p-7 flex flex-col gap-5 transition-all duration-300 hover:shadow-xl hover:shadow-brand-soft">
            <div class="flex items-center justify-between">
                <span class="w-12 h-12 rounded-2xl bg-brand-soft text-brand flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-sm">
                    <i class="ph ph-phone text-2xl"></i>
                </span>
                <span class="text-[10px] font-bold text-brand bg-brand-soft px-2.5 py-1 rounded-full uppercase tracking-wider">{{ __('Hotline') }}</span>
            </div>
            <div>
                <h4 class="font-extrabold text-ink text-base mb-2 group-hover:text-brand transition-colors">{{ __('Call Our Team') }}</h4>
                <p class="text-neutral-500 text-sm leading-relaxed">{{ __('Speak with a style assistant for instant help with orders, returns, and delivery.') }}</p>
            </div>
            <a href="tel:{{ config('application_info.company_info.phone') }}"
               class="mt-auto w-full flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold text-xs uppercase tracking-wider py-3 px-5 rounded-xl transition-colors shadow-md shadow-brand/20">
                <i class="ph ph-phone-call text-base"></i>
                {{ __('Call Now') }}
            </a>
        </div>

    </div>
</section>

@endsection
