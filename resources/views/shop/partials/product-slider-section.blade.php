{{-- Reusable Swiper slider section: $title, $products (collection), $viewAll (url) --}}
@if ($products->isNotEmpty())
@php $sliderId = 'swiper-' . Str::slug($title); @endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<section class="shop-container mt-14  group/slider">

    {{-- Heading --}}
    <div class="relative w-full flex flex-col items-center mb-6">
        <div class="section-heading mb-0">
            <span class="eyebrow">{{ $eyebrow ?? __('Exclusive') }}</span>
            <h2>{{ $title }}</h2>
        </div>
    </div>

    <div class="relative px-0">

        {{-- Prev arrow --}}
        <button type="button" id="{{ $sliderId }}-prev"
            class="absolute left-0 top-[35%] -translate-y-1/2 z-20
                   w-8 h-12 rounded-r-full
                   bg-brand-soft text-brand hover:bg-brand hover:text-white
                   flex items-center justify-start pl-2
                   border border-l-0 border-brand-mist hover:border-brand transition-all duration-300
                   lg:opacity-0 lg:group-hover/slider:opacity-100 shadow-sm hover:shadow-md
                   disabled:opacity-25 disabled:pointer-events-none cursor-pointer">
            <i class="ph-bold ph-caret-left text-base"></i>
        </button>

        {{-- Next arrow --}}
        <button type="button" id="{{ $sliderId }}-next"
            class="absolute right-0 top-[35%] -translate-y-1/2 z-20
                   w-8 h-12 rounded-l-full
                   bg-brand-soft text-brand hover:bg-brand hover:text-white
                   flex items-center justify-end pr-2
                   border border-r-0 border-brand-mist hover:border-brand transition-all duration-300
                   lg:opacity-0 lg:group-hover/slider:opacity-100 shadow-sm hover:shadow-md
                   disabled:opacity-25 disabled:pointer-events-none cursor-pointer">
            <i class="ph-bold ph-caret-right text-base"></i>
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

    {{-- View all --}}
    @if (isset($viewAll))
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
        spaceBetween: 12,
        navigation: {
            nextEl: '#{{ $sliderId }}-next',
            prevEl: '#{{ $sliderId }}-prev',
        },
        breakpoints: {
            475:  { slidesPerView: 2, spaceBetween: 14 },
            768:  { slidesPerView: 3, spaceBetween: 16 },
            1024: { slidesPerView: 4, spaceBetween: 16 },
            1280: { slidesPerView: 4, spaceBetween: 16 },
        },
    });
});
</script>
@endif
