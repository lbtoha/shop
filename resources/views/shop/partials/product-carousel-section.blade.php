{{-- Auto-playing, looping product carousel: $title, $eyebrow, $products, $viewAll, $uid --}}
@if ($products->isNotEmpty())
@php $sliderId = 'carousel-' . ($uid ?? Str::slug($title)); @endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<section class="shop-container shop-section-gap group/slider">

    {{-- Heading --}}
    <div class="relative w-full flex flex-col items-center mb-6">
        <div class="section-heading mb-0">
            <span class="eyebrow">{{ $eyebrow ?? __('Collection') }}</span>
            <h2>{{ $title }}</h2>
        </div>
    </div>

    <div class="relative px-0">
        <button type="button" id="{{ $sliderId }}-prev"
            class="hidden sm:flex absolute -left-4 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-brand-soft text-brand hover:bg-brand hover:text-white items-center justify-center border-4 border-white transition-all duration-200 lg:opacity-0 lg:group-hover/slider:opacity-100 shadow-md hover:shadow-lg cursor-pointer disabled:opacity-20 disabled:pointer-events-none">
            <i class="ph-bold ph-caret-left text-base"></i>
        </button>
        <button type="button" id="{{ $sliderId }}-next"
            class="hidden sm:flex absolute -right-4 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-brand-soft text-brand hover:bg-brand hover:text-white items-center justify-center border-4 border-white transition-all duration-200 lg:opacity-0 lg:group-hover/slider:opacity-100 shadow-md hover:shadow-lg cursor-pointer disabled:opacity-20 disabled:pointer-events-none">
            <i class="ph-bold ph-caret-right text-base"></i>
        </button>

        <div class="swiper {{ $sliderId }} overflow-hidden pb-1">
            <div class="swiper-wrapper items-stretch">
                @foreach ($products as $product)
                    <div class="swiper-slide h-auto px-1.5 min-[475px]:px-2">
                        <x-shop::product-card :product="$product" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if (!empty($viewAll))
        <div class="flex justify-center mt-8">
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
        slidesPerView: 1,
        spaceBetween: 0,
        centeredSlides: true,
        loop: {{ $products->count() > 4 ? 'true' : 'false' }},
        autoplay: { delay: 3500, disableOnInteraction: false, pauseOnMouseEnter: true },
        navigation: {
            nextEl: '#{{ $sliderId }}-next',
            prevEl: '#{{ $sliderId }}-prev',
        },
        breakpoints: {
            475:  { slidesPerView: 2, spaceBetween: 0, centeredSlides: false },
            768:  { slidesPerView: 3, spaceBetween: 0, centeredSlides: false },
            1024: { slidesPerView: 4, spaceBetween: 0, centeredSlides: false },
            1280: { slidesPerView: 4, spaceBetween: 0, centeredSlides: false },
        },
    });
});
</script>
@endif
