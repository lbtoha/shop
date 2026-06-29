@extends('shop.layouts.app')

@section('title', config('application_info.company_info.name') . ' — ' . __('Premium Fashion & Lifestyle'))

@section('content')

{{-- ── Hero Carousel ─────────────────────────────────────────── --}}
@if ($banners->isNotEmpty())
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<style>
.hero-swiper-pagination .swiper-pagination-bullet {
    width: 16px;
    height: 12px;
    background: #f59caf;
    opacity: 1;
    border-radius: 9999px;
    border: 2px solid #fff;
    transition: all 0.3s ease;
    margin: 0 3px !important;
}
.hero-swiper-pagination .swiper-pagination-bullet-active {
    width: 32px;
    background: #e11d48 !important;
}
</style>

<div class="shop-container mt-5 sm:mt-6">
    <div class="swiper hero-swiper relative w-full h-auto aspect-[2.7/1] sm:aspect-[3/1] lg:aspect-[3.2/1] xl:aspect-[3.5/1] rounded-2xl group/hero bg-neutral-100 shadow-sm overflow-hidden">
        <div class="swiper-wrapper">
            @foreach ($banners as $banner)
                @php
                    $targetLink = null;
                    if ($banner->category) {
                        $targetLink = route('shop.index', ['category' => $banner->category->slug]);
                    } elseif ($banner->link) {
                        $targetLink = $banner->link;
                    }
                @endphp
                <div class="swiper-slide w-full h-full">
                    @if ($targetLink)
                        <a href="{{ $targetLink }}" class="block w-full h-full overflow-hidden">
                    @else
                        <div class="block w-full h-full overflow-hidden">
                    @endif
                        <img src="{{ $banner->image }}" alt="Banner"
                             class="w-full h-full object-cover">
                    @if ($targetLink)
                        </a>
                    @else
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if ($banners->count() > 1)
            {{-- Controls --}}
            <button type="button" class="hero-swiper-prev hidden sm:flex absolute left-0 top-1/2 -translate-y-1/2 z-20 w-8 h-8  rounded-r-full bg-brand-soft text-brand hover:bg-brand hover:text-white items-center justify-start pl-2 border-4 border-l-0 border-white  transition-all duration-200 lg:opacity-0 lg:group-hover/hero:opacity-100 shadow-md hover:shadow-lg cursor-pointer"> 
                <i class="ph-bold ph-caret-left text-base"></i>
            </button>
            <button type="button" class="hero-swiper-next hidden sm:flex absolute right-0 top-1/2 -translate-y-1/2 z-20 w-8 h-8  rounded-l-full bg-brand-soft text-brand hover:bg-brand hover:text-white items-center justify-end pr-2 border-4 border-r-0 border-white  transition-all duration-200 lg:opacity-0 lg:group-hover/hero:opacity-100 shadow-md hover:shadow-lg cursor-pointer">
                <i class="ph-bold ph-caret-right text-base"></i>
            </button>

            {{-- Dots --}}
            <div class="hero-swiper-pagination absolute bottom-5 -left-1/2! translate-x-1/2 z-20 flex gap-1.5 justify-center items-center"></div>
        @endif
    </div>
</div>

<!-- Swiper JS & Init -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Swiper('.hero-swiper', {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.hero-swiper-next',
            prevEl: '.hero-swiper-prev',
        },
        pagination: {
            el: '.hero-swiper-pagination',
            clickable: true,
        },
        speed: 800,
    });
});
</script>
@endif

<div class="shop-container">

    {{-- ── Shop By Category ──────────────────────────────────── --}}
    @if ($categories->isNotEmpty())
        <style>
        .category-swiper-pagination {
            position: absolute;
            bottom: 0px !important;
            left: 50% !important;
            transform: translateX(-50%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }
        .category-swiper-pagination .swiper-pagination-bullet {
            width: 8px;
            height: 8px;
            background: rgba(0, 0, 0, 0.2);
            opacity: 1;
            border-radius: 9999px;
            transition: all 0.3s ease;
            margin: 0 4px !important;
            cursor: pointer;
        }
        .category-swiper-pagination .swiper-pagination-bullet-active {
            width: 20px;
            background: #e11d48 !important;
        }
        </style>

        <section class="shop-section-gap relative group/cat">
            <div class="relative w-full flex flex-col items-center mb-4">
                <div class="section-heading mb-0">
                    <span class="eyebrow">{{ __('Browse') }}</span>
                    <h2>{{ __('Shop by Category') }}</h2>
                </div>
            </div>
            
            <div class="relative px-0">
                {{-- Prev arrow --}}
                <button type="button" id="cat-swiper-prev"
                    class="hidden sm:flex absolute -left-4 top-1/2 -translate-y-1/2 z-20
                           w-8 h-8 rounded-full bg-brand-soft text-brand hover:bg-brand hover:text-white
                           items-center justify-center border-4 border-white
                           transition-all duration-200 lg:opacity-0 lg:group-hover/cat:opacity-100 shadow-md hover:shadow-lg cursor-pointer
                           disabled:opacity-20 disabled:pointer-events-none">
                    <i class="ph-bold ph-caret-left text-base"></i>
                </button>

                {{-- Next arrow --}}
                <button type="button" id="cat-swiper-next"
                    class="hidden sm:flex absolute -right-4 top-1/2 -translate-y-1/2 z-20
                           w-8 h-8 rounded-full bg-brand-soft text-brand hover:bg-brand hover:text-white
                           items-center justify-center border-4 border-white
                           transition-all duration-200 lg:opacity-0 lg:group-hover/cat:opacity-100 shadow-md hover:shadow-lg cursor-pointer
                           disabled:opacity-20 disabled:pointer-events-none">
                    <i class="ph-bold ph-caret-right text-base"></i>
                </button>

                <div class="swiper category-swiper overflow-hidden pb-10">
                    <div class="swiper-wrapper">
                        @foreach ($categories as $category)
                             <div class="swiper-slide flex justify-center py-2 px-1.5 min-[400px]:px-2 sm:px-2.5 lg:px-3">
                                <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                                   class="mx-auto flex flex-col items-center bg-brand/5 hover:bg-brand/10   rounded-xl p-4 sm:p-5 w-full max-w-[300px]     transition-all duration-300">
                                    <div class="size-28 sm:size-32 md:size-40  rounded-full overflow-hidden bg-neutral-50 mb-3 transition-all duration-500 flex items-center justify-center">
                                        <img src="{{ $category->image }}" alt="{{ $category->name }}"
                                             class="w-full h-full object-cover transition-transform duration-500 ">
                                    </div>
                                    <div class="text-center w-full">
                                        <span class="block text-sm sm:text-md font-semibold text-ink uppercase tracking-wide group-hover:text-brand transition-colors duration-200 line-clamp-2 leading-snug">
                                            {{ $category->name }}
                                        </span>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Pagination dots --}}
                    <div class="category-swiper-pagination"></div>
                </div>
            </div>
        </section>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Swiper('.category-swiper', {
                slidesPerView: 1,
                spaceBetween: 0,
                centeredSlides: true,
                centerInsufficientSlides: true,
                loop: {{ $categories->count() > 1 ? 'true' : 'false' }},
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                   
                },
                navigation: {
                    nextEl: '#cat-swiper-next',
                    prevEl: '#cat-swiper-prev',
                },
                breakpoints: {
                    400:  { slidesPerView: 2, spaceBetween: 0, centeredSlides: false },
                    768:  { slidesPerView: 3, spaceBetween: 0, centeredSlides: false },
                    1024: { slidesPerView: 4, spaceBetween: 0, centeredSlides: false },
                    1280: { slidesPerView: 5, spaceBetween: 0, centeredSlides: false },
                },
            });
        });
        </script>
    @endif

</div>

{{-- ── Dynamic home sections (database-driven, configured in admin → Home Sections) ── --}}
@foreach ($sections as $section)
    <x-shop::home-section :section="$section" />
@endforeach



@endsection
