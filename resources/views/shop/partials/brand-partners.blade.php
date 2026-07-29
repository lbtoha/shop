@php
    $partners = getOptionWithJsonDecode('brand_partners', []);
    $partnerCount = count($partners);
@endphp

@if ((int) getOption('show_brand_partners', 1) === 1)
    <!-- Swiper Asset Dependencies -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <style>
        .brand-partners-section {
            padding-top: 2.5rem !important; /* 40px */
        }
        @media (min-width: 640px) {
            .brand-partners-section {
                padding-top: 5rem !important; /* 80px */
            }
        }
    </style>

    <section class="shop-container brand-partners-section mb-12">
        <div class="border-t border-line pt-12">
            <div class="section-heading mb-10">
                <span class="eyebrow">{{ __('Partners') }}</span>
                <h2>{{ __('Our Brand Partners') }}</h2>
            </div>

            <!-- Brand Partners Swiper Container -->
            <div class="swiper brand-partners-swiper overflow-hidden py-4">
                <div class="swiper-wrapper items-center">
                    @if ($partnerCount > 0)
                        @foreach ($partners as $p)
                            <div class="swiper-slide flex items-center justify-center px-4">
                                @if ($p['logo'])
                                    <img src="{{ asset($p['logo']) }}" alt="{{ $p['name'] ?? __('Partner') }}" class="max-h-12 sm:max-h-16 max-w-full w-auto object-contain block mx-auto" />
                                @else
                                    <span class="border border-line px-4 py-2 bg-neutral-50 rounded-md text-neutral-600 text-xs sm:text-sm font-bold uppercase tracking-wider whitespace-nowrap select-none">
                                        {{ $p['name'] }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <!-- Default SVG Fallbacks -->
                        <div class="swiper-slide flex items-center justify-center px-4">
                            <svg class="h-8 sm:h-10 w-auto text-neutral-400" viewBox="0 0 120 40" fill="currentColor">
                                <text x="10" y="28" font-family="'Outfit', sans-serif" font-size="22" font-weight="800" letter-spacing="4">LUXE</text>
                                <line x1="10" y1="32" x2="110" y2="32" stroke="currentColor" stroke-width="2" />
                            </svg>
                        </div>
                        <div class="swiper-slide flex items-center justify-center px-4">
                            <svg class="h-8 sm:h-10 w-auto text-neutral-400" viewBox="0 0 140 40" fill="currentColor">
                                <circle cx="20" cy="20" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                                <path d="M17 17 C 20 14, 24 18, 20 22 C 16 26, 22 24, 20 20" fill="none" stroke="currentColor" stroke-width="1.5"/>
                                <text x="38" y="26" font-family="'Outfit', sans-serif" font-size="15" font-weight="600" letter-spacing="1">COTTON CO.</text>
                            </svg>
                        </div>
                        <div class="swiper-slide flex items-center justify-center px-4">
                            <svg class="h-8 sm:h-10 w-auto text-neutral-400" viewBox="0 0 130 40" fill="currentColor">
                                <text x="5" y="27" font-family="'Outfit', sans-serif" font-style="italic" font-size="20" font-weight="500" letter-spacing="2">Elegance</text>
                            </svg>
                        </div>
                        <div class="swiper-slide flex items-center justify-center px-4">
                            <svg class="h-8 sm:h-10 w-auto text-neutral-400" viewBox="0 0 120 40" fill="currentColor">
                                <text x="10" y="28" font-family="'Georgia', serif" font-size="24" font-weight="bold" letter-spacing="3">VOGUE</text>
                            </svg>
                        </div>
                        <div class="swiper-slide flex items-center justify-center px-4">
                            <svg class="h-6 sm:h-8 w-auto text-neutral-400" viewBox="0 0 150 40" fill="currentColor">
                                <rect x="5" y="8" width="140" height="24" rx="3" fill="none" stroke="currentColor" stroke-width="2"/>
                                <text x="12" y="24" font-family="'Outfit', sans-serif" font-size="11" font-weight="900" letter-spacing="2">PREMIUM WEAR</text>
                            </svg>
                        </div>
                        <div class="swiper-slide flex items-center justify-center px-4">
                            <svg class="h-8 sm:h-10 w-auto text-neutral-400" viewBox="0 0 140 40" fill="currentColor">
                                <path d="M10 10 L 25 10 L 25 30 L 10 30 Z" fill="none" stroke="currentColor" stroke-width="2"/>
                                <path d="M15 15 L 20 15 L 20 25 L 15 25 Z" fill="currentColor"/>
                                <text x="35" y="26" font-family="'Outfit', sans-serif" font-size="14" font-weight="700" letter-spacing="1.5">GALLERY</text>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        new Swiper('.brand-partners-swiper', {
            slidesPerView: 2,
            spaceBetween: 20,
            centerInsufficientSlides: true,
            loop: {{ ($partnerCount === 0 || $partnerCount >= 6) ? 'true' : 'false' }},
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
                pauseOnMouseEnter: true
            },
            speed: 1000,
            breakpoints: {
                475:  { slidesPerView: 3, spaceBetween: 25 },
                768:  { slidesPerView: 4, spaceBetween: 30 },
                1024: { slidesPerView: 5, spaceBetween: 35 },
                1280: { slidesPerView: 6, spaceBetween: 40 },
            },
        });
    });
    </script>
@endif
