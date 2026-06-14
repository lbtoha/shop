{{-- Reusable Swiper slider section: $title, $products (collection), $viewAll (url) --}}
@if ($products->isNotEmpty())
@php $sliderId = 'swiper-' . Str::slug($title); @endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<section class="shop-container mt-14 sm:mt-16 group/slider">

    {{-- Heading --}}
    <div class="flex items-end justify-between gap-4 mb-6">
        <div class="section-heading is-start mb-0">
            <h2>{{ $title }}</h2>
        </div>
        @if (isset($viewAll))
            <a href="{{ $viewAll }}" class="btn-outline shrink-0 hidden sm:inline-flex">
                {{ __('View All') }}
                <i class="ph ph-arrow-right text-sm"></i>
            </a>
        @endif
    </div>

    {{-- Slider wrapper --}}
    <div class="relative px-0 sm:px-10">

        {{-- Prev arrow --}}
        <button type="button" id="{{ $sliderId }}-prev"
            class="absolute -left-1 sm:left-0 top-[45%] -translate-y-1/2 z-20
                   w-9 h-9 sm:w-10 sm:h-10 rounded-xl
                   bg-white border border-line shadow-md
                   flex items-center justify-center
                   text-ink hover:text-brand hover:border-brand hover:shadow-lg
                   transition-all duration-200
                   opacity-0 group-hover/slider:opacity-100 focus:opacity-100
                   disabled:opacity-25 disabled:cursor-not-allowed cursor-pointer">
            <i class="ph ph-caret-left text-lg font-bold"></i>
        </button>

        {{-- Next arrow --}}
        <button type="button" id="{{ $sliderId }}-next"
            class="absolute -right-1 sm:right-0 top-[45%] -translate-y-1/2 z-20
                   w-9 h-9 sm:w-10 sm:h-10 rounded-xl
                   bg-white border border-line shadow-md
                   flex items-center justify-center
                   text-ink hover:text-brand hover:border-brand hover:shadow-lg
                   transition-all duration-200
                   opacity-0 group-hover/slider:opacity-100 focus:opacity-100
                   disabled:opacity-25 disabled:cursor-not-allowed cursor-pointer">
            <i class="ph ph-caret-right text-lg font-bold"></i>
        </button>

        {{-- Swiper --}}
        <div class="swiper {{ $sliderId }} overflow-hidden pb-1">
            <div class="swiper-wrapper items-stretch">
                @foreach ($products as $product)
                    <div class="swiper-slide h-auto">
                        <x-shop::product-card :product="$product" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- View all (mobile) --}}
    @if (isset($viewAll))
        <div class="flex justify-center mt-7 sm:hidden">
            <a href="{{ $viewAll }}" class="btn-outline">
                {{ __('View All') }}
                <i class="ph ph-arrow-right text-sm"></i>
            </a>
        </div>
    @endif

</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    new Swiper('.{{ $sliderId }}', {
        slidesPerView: 2,
        spaceBetween: 12,
        navigation: {
            nextEl: '#{{ $sliderId }}-next',
            prevEl: '#{{ $sliderId }}-prev',
        },
        breakpoints: {
            480:  { slidesPerView: 2, spaceBetween: 14 },
            768:  { slidesPerView: 3, spaceBetween: 18 },
            1024: { slidesPerView: 4, spaceBetween: 20 },
            1280: { slidesPerView: 4, spaceBetween: 24 },
        },
    });
});
</script>
@endif
