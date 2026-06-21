@extends('shop.layouts.app')

@section('title', config('application_info.company_info.name') . ' — ' . __('Premium Fashion & Lifestyle'))

@section('content')

{{-- ── Hero Carousel ─────────────────────────────────────────── --}}
@if ($banners->isNotEmpty())
<div class="shop-container mt-5 sm:mt-6">
    <div class="relative w-full h-[320px] xs:h-[380px] sm:h-[440px] lg:h-[520px]  overflow-hidden group/hero bg-ink"
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
                <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/50 to-transparent lg:from-black/85 lg:via-black/40 lg:to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>

                {{-- Slide content --}}
                <div class="absolute inset-0 flex items-center">
                    <div class="px-6 sm:px-12 lg:px-16 space-y-2.5 xs:space-y-4 sm:space-y-5 max-w-[85%] sm:max-w-xl content-anim">
                        {{-- Eyebrow badge --}}
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 xs:px-3 xs:py-1.5 rounded-full bg-white/10 border border-white/20 backdrop-blur-sm text-white">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-light animate-pulse"></span>
                            <span class="text-[9px] xs:text-[10px] sm:text-xs font-bold uppercase tracking-[0.15em]">{{ __('Special Offer') }}</span>
                        </span>

                        {{-- Title --}}
                        <h1 class="text-xl xs:text-2xl sm:text-4xl lg:text-5xl font-black uppercase tracking-tight leading-[1.1] text-white">
                            {!! str_replace(' & ', ' <span class="text-brand-light">&</span> ', e(__($banner->title))) !!}
                        </h1>

                        {{-- Subtitle --}}
                        <p class="text-xs xs:text-sm sm:text-base text-white/80 font-medium max-w-xs sm:max-w-md leading-relaxed">
                            {{ __($banner->subtitle) }}
                        </p>

                        {{-- CTA --}}
                        <div class="flex items-center gap-4 pt-1 sm:pt-2">
                            <a href="{{ $banner->link ?: route('shop.index') }}"
                               class="btn-brand text-xs py-2.5 px-5 xs:text-sm xs:py-3 xs:px-7 group/btn">
                                <span>{{ __($banner->button_text ?: 'Shop Now') }}</span>
                                <i class="ph ph-arrow-right transition-transform duration-200 group-hover/btn:translate-x-1"></i>
                            </a>
                            <a href="{{ route('shop.index') }}"
                               class="text-xs xs:text-sm text-white/75 hover:text-white font-semibold underline underline-offset-4 transition-colors">
                               {{ __('Browse All') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Controls (always visible on touch, fade-in on desktop hover) --}}
        <button type="button" data-hero-prev
            class="hidden sm:flex absolute left-0 top-1/2 -translate-y-1/2 z-20 w-8 h-12 rounded-r-full bg-brand-soft text-brand hover:bg-brand hover:text-white items-center justify-start pl-2 border border-l-0 border-brand-mist hover:border-brand transition-all duration-200 lg:opacity-0 lg:group-hover/hero:opacity-100 shadow-md hover:shadow-lg cursor-pointer">
            <i class="ph-bold ph-caret-left text-base"></i>
        </button>
        <button type="button" data-hero-next
            class="hidden sm:flex absolute right-0 top-1/2 -translate-y-1/2 z-20 w-8 h-12 rounded-l-full bg-brand-soft text-brand hover:bg-brand hover:text-white items-center justify-end pr-2 border border-r-0 border-brand-mist hover:border-brand transition-all duration-200 lg:opacity-0 lg:group-hover/hero:opacity-100 shadow-md hover:shadow-lg cursor-pointer">
            <i class="ph-bold ph-caret-right text-base"></i>
        </button>

        {{-- Dots --}}
        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-20 flex gap-1.5">
            @foreach ($banners as $i => $banner)
                <button type="button" data-hero-dot="{{ $i }}"
                    class="{{ $i === 0 ? 'bg-brand w-8' : 'bg-white/35 w-3' }} h-1.5 rounded-full hover:bg-white/60 transition-all duration-300 cursor-pointer"
                    aria-label="Slide {{ $i + 1 }}"></button>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="shop-container">



    {{-- ── Shop By Category ──────────────────────────────────── --}}
    @if ($categories->isNotEmpty())
        <section class="mt-14 sm:mt-16">
            <div class="section-heading">
                <span class="eyebrow">{{ __('Browse') }}</span>
                <h2>{{ __('Shop by Category') }}</h2>
            </div>
            <div class="flex flex-wrap items-start justify-center gap-6 sm:gap-10 md:gap-12">
                @foreach ($categories as $category)
                    <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                       class="category-pill group">
                        <div class="cp-img">
                            <img src="{{ $category->image }}" alt="{{ $category->name }}"
                                 class="transition-transform duration-500 group-hover:scale-110">
                        </div>
                        <div class="text-center">
                            <span class="block text-xs sm:text-[13px] font-bold text-ink uppercase tracking-wider group-hover:text-brand transition-colors duration-200 line-clamp-2 leading-snug max-w-[100px] sm:max-w-[120px]">
                                {{ $category->name }}
                            </span>
                            <span class="block text-[11px] text-muted mt-0.5">{{ $category->products_count ?? 0 }} {{ __('products') }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

</div>

{{-- ── Dynamic home sections (database-driven, configured in admin → Home Sections) ── --}}
@foreach ($sections as $section)
    <x-shop::home-section :section="$section" />
@endforeach



@endsection
