{{-- Reusable homepage product slider section using Swiper: $title, $subtitle?, $products (collection), $viewAll (url) --}}
@if ($products->isNotEmpty())
    <!-- Load Swiper CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <section class="shop-container mt-16 relative group/slider">
        <!-- Centered Heading with Dividers -->
        <div class="flex items-center gap-4 mb-10">
            <div class="flex-grow border-t border-neutral-200/80"></div>
            <h2 class="text-xl sm:text-2xl font-bold text-neutral-800 tracking-wide text-center px-4">
                {{ $title }}
            </h2>
            <div class="flex-grow border-t border-neutral-200/80"></div>
        </div>

        <!-- Slider Wrapper with Arrows -->
        <div class="relative px-0 sm:px-8">
            <!-- Left Arrow Button -->
            <button type="button" 
                class="absolute -left-2 sm:left-0 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/95 border border-neutral-200 shadow-md flex items-center justify-center text-neutral-600 hover:text-brand hover:border-brand hover:scale-105 active:scale-95 transition-all opacity-0 group-hover/slider:opacity-100 focus:opacity-100 disabled:opacity-30 disabled:cursor-not-allowed"
                id="slider-prev-{{ Str::slug($title) }}">
                <i class="ph ph-caret-left text-xl font-bold"></i>
            </button>

            <!-- Right Arrow Button -->
            <button type="button" 
                class="absolute -right-2 sm:right-0 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/95 border border-neutral-200 shadow-md flex items-center justify-center text-neutral-600 hover:text-brand hover:border-brand hover:scale-105 active:scale-95 transition-all opacity-0 group-hover/slider:opacity-100 focus:opacity-100 disabled:opacity-30 disabled:cursor-not-allowed"
                id="slider-next-{{ Str::slug($title) }}">
                <i class="ph ph-caret-right text-xl font-bold"></i>
            </button>

            <!-- Swiper Container -->
            <div class="swiper featured-swiper-{{ Str::slug($title) }} overflow-hidden pb-4">
                <div class="swiper-wrapper">
                    @foreach ($products as $product)
                        <div class="swiper-slide h-auto">
                            <x-shop::product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Bottom Centered View All -->
        @if (isset($viewAll))
            <div class="flex justify-center mt-6">
                <a href="{{ $viewAll }}" class="inline-flex items-center gap-2 px-6 py-2.5 border border-neutral-200 hover:border-brand hover:text-brand rounded-2xl text-xs font-black text-neutral-600 transition-all duration-300 uppercase tracking-wider bg-white shadow-sm hover:shadow-md">
                    <span>{{ __('View All') }}</span>
                    <i class="ph ph-arrow-right text-sm"></i>
                </a>
            </div>
        @endif

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Swiper
                const swiper = new Swiper('.featured-swiper-{{ Str::slug($title) }}', {
                    slidesPerView: 2,
                    spaceBetween: 16,
                    navigation: {
                        nextEl: '#slider-next-{{ Str::slug($title) }}',
                        prevEl: '#slider-prev-{{ Str::slug($title) }}',
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 2,
                            spaceBetween: 20,
                        },
                        768: {
                            slidesPerView: 3,
                            spaceBetween: 24,
                        },
                        1024: {
                            slidesPerView: 4,
                            spaceBetween: 24,
                        }
                    }
                });
            });
        </script>
    </section>
@endif
