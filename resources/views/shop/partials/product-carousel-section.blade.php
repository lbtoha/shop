{{-- Auto-playing, looping product carousel: $title, $eyebrow, $products, $viewAll, $uid --}}
@if ($products->isNotEmpty())
@php $sliderId = 'carousel-' . ($uid ?? Str::slug($title)); @endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<section class="shop-container mt-14 group/slider">

    {{-- Heading --}}
    <div class="relative w-full flex flex-col items-center mb-6">
        <div class="section-heading mb-0">
            <span class="eyebrow">{{ $eyebrow ?? __('Collection') }}</span>
            <h2>{{ $title }}</h2>
        </div>
    </div>

    <div class="relative px-0 sm:px-10">
        <button type="button" id="{{ $sliderId }}-prev"
            class="absolute -left-1 sm:left-0 top-[45%] -translate-y-1/2 z-20 w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white border border-line shadow-md flex items-center justify-center text-ink hover:text-brand hover:border-brand hover:shadow-lg transition-all duration-200 opacity-0 group-hover/slider:opacity-100 focus:opacity-100 cursor-pointer">
            <i class="ph ph-caret-left text-lg font-bold"></i>
        </button>
        <button type="button" id="{{ $sliderId }}-next"
            class="absolute -right-1 sm:right-0 top-[45%] -translate-y-1/2 z-20 w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white border border-line shadow-md flex items-center justify-center text-ink hover:text-brand hover:border-brand hover:shadow-lg transition-all duration-200 opacity-0 group-hover/slider:opacity-100 focus:opacity-100 cursor-pointer">
            <i class="ph ph-caret-right text-lg font-bold"></i>
        </button>

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
        slidesPerView: 2,
        spaceBetween: 12,
        loop: {{ $products->count() > 4 ? 'true' : 'false' }},
        autoplay: { delay: 3500, disableOnInteraction: false, pauseOnMouseEnter: true },
        navigation: {
            nextEl: '#{{ $sliderId }}-next',
            prevEl: '#{{ $sliderId }}-prev',
        },
        breakpoints: {
            480:  { slidesPerView: 2, spaceBetween: 14 },
            768:  { slidesPerView: 3, spaceBetween: 16 },
            1024: { slidesPerView: 4, spaceBetween: 16 },
            1280: { slidesPerView: 4, spaceBetween: 16 },
        },
    });
});
</script>
@endif
