@extends('shop.layouts.app')

@section('title', config('application_info.company_info.name') . ' — ' . __('Premium Ethnic Wear'))

@section('content')
    {{-- Premium Hero Section --}}
    @if ($banners->isNotEmpty())
        <div class="shop-container mt-6">
            <!-- Main Slider -->
            <div class="w-full h-[300px] sm:h-[450px] lg:h-[500px] rounded-3xl overflow-hidden relative group/hero" data-hero>
                <!-- Slides -->
                <div class="relative w-full h-full">
                    @foreach ($banners as $i => $banner)
                        <div data-slide class="{{ $i === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 hidden' }} absolute inset-0 w-full h-full transition-all duration-1000 ease-in-out">
                            <!-- Image Container with Zoom effect on active -->
                            <div class="absolute inset-0 w-full h-full overflow-hidden bg-neutral-900">
                                <img src="{{ $banner->image }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                                <!-- Overlay Gradient -->
                                <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/35 to-transparent"></div>
                            </div>
                            
                            <!-- Content Area -->
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full px-6 sm:px-12 lg:px-16 text-white space-y-4 sm:space-y-6 content-anim">
                                    <!-- Badge -->
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand/20 border border-brand/30 backdrop-blur-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-brand animate-pulse"></span>
                                        <span class="text-[10px] sm:text-xs font-bold uppercase tracking-widest text-brand-light">
                                            {{ __('Special Offer') }}
                                        </span>
                                    </div>
                                    
                                    <!-- Title -->
                                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black uppercase tracking-tight leading-tight">
                                        {!! str_replace(' & ', ' <span class="text-brand">&</span> ', e(__($banner->title))) !!}
                                    </h1>
                                    
                                    <!-- Subtitle -->
                                    <p class="text-xs sm:text-sm text-neutral-300 font-medium max-w-md leading-relaxed">
                                        {{ __($banner->subtitle) }}
                                    </p>
                                    
                                    <!-- Action Button -->
                                    <div class="pt-2">
                                        <a href="{{ $banner->link ?: route('shop.index') }}" 
                                           class="inline-flex items-center gap-2.5 bg-brand hover:bg-brand-dark text-white font-extrabold px-6 py-3 rounded-xl transition duration-300 text-xs sm:text-sm tracking-wider uppercase shadow-lg shadow-brand/20 hover:shadow-brand/40 group/btn">
                                            <span>{{ __($banner->button_text ?: 'Shop Now') }}</span>
                                            <i class="ph ph-arrow-right text-base transition-transform duration-300 group-hover/btn:translate-x-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Next / Prev Controls -->
                <button type="button" data-hero-prev class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-black/30 hover:bg-brand text-white flex items-center justify-center transition opacity-0 group-hover/hero:opacity-100 backdrop-blur-sm border border-white/10 hover:border-brand shadow-lg cursor-pointer">
                    <i class="ph ph-caret-left text-xl sm:text-2xl"></i>
                </button>
                <button type="button" data-hero-next class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-black/30 hover:bg-brand text-white flex items-center justify-center transition opacity-0 group-hover/hero:opacity-100 backdrop-blur-sm border border-white/10 hover:border-brand shadow-lg cursor-pointer">
                    <i class="ph ph-caret-right text-xl sm:text-2xl"></i>
                </button>

                <!-- Indicators / Dot Navigation -->
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex gap-2">
                    @foreach ($banners as $i => $banner)
                        <button type="button" data-hero-dot="{{ $i }}" class="w-3 h-1.5 rounded-full bg-white/40 hover:bg-white/70 transition-all duration-300 cursor-pointer" aria-label="Go to slide {{ $i + 1 }}"></button>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Main Container --}}
    <div class="shop-container">
        {{-- Info Strip / Feature List --}}
        <section class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $features = [
                    ['icon' => 'ph-truck', 'title' => __('Free Delivery'), 'text' => __('Orders above ৳5000')],
                    ['icon' => 'ph-arrow-counter-clockwise', 'title' => __('Easy Returns'), 'text' => __('7 days exchange policy')],
                    ['icon' => 'ph-hand-coins', 'title' => __('Cash on Delivery'), 'text' => __('Pay at your doorstep')],
                    ['icon' => 'ph-shield-check', 'title' => __('Safe Shopping'), 'text' => __('100% genuine products')],
                ];
            @endphp
            @foreach ($features as $f)
                <div class="group bg-gradient-to-br from-white to-neutral-50/50 border border-neutral-200/50 rounded-3xl p-5 flex items-center gap-4.5 shadow-sm hover:shadow-xl hover:shadow-brand/5 hover:border-brand/30 hover:-translate-y-1.5 transition-all duration-300 relative overflow-hidden">
                    <!-- Glow effect on hover -->
                    <div class="absolute -right-8 -bottom-8 w-24 h-24 bg-brand/5 rounded-full blur-2xl group-hover:bg-brand/10 transition-colors duration-300"></div>

                    <!-- Icon Container with custom gradient -->
                    <div class="w-13 h-13 rounded-2xl bg-gradient-to-br from-brand to-brand-dark border border-brand/20 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-brand/25 transition-all duration-300 shadow-md shadow-brand/10">
                        <i class="ph {{ $f['icon'] }} text-2xl text-white transition-all duration-300 group-hover:rotate-12"></i>
                    </div>

                    <!-- Text contents -->
                    <div class="min-w-0 relative z-10">
                        <h4 class="text-sm font-extrabold text-neutral-800 tracking-wide uppercase leading-tight group-hover:text-brand transition-colors duration-200">
                            {{ $f['title'] }}
                        </h4>
                        <p class="text-[11px] sm:text-xs text-neutral-400 font-semibold mt-1.5 leading-normal">
                            {{ $f['text'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- Shop By Category Section --}}
        @if ($categories->isNotEmpty())
            <section class="mt-16">
                <!-- Centered Heading with Dividers -->
                <div class="flex items-center gap-4 mb-10">
                    <div class="flex-grow border-t border-neutral-200/80"></div>
                    <h2 class="text-xl sm:text-2xl font-bold text-neutral-800 tracking-wide text-center px-4">
                        {{ __('Categories') }}
                    </h2>
                    <div class="flex-grow border-t border-neutral-200/80"></div>
                </div>

                <!-- Circular Items Grid -->
                <div class="flex flex-wrap items-center justify-center gap-8 sm:gap-12 md:gap-16">
                    @foreach ($categories as $category)
                        <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="group flex flex-col items-center max-w-[120px] sm:max-w-[140px]">
                            <!-- Circle Image Container -->
                            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full border-4 border-white shadow-md overflow-hidden transition-all duration-300 group-hover:shadow-lg group-hover:border-brand/40 group-hover:scale-105 shrink-0 bg-neutral-100">
                                <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            </div>
                            <!-- Title Label -->
                            <span class="mt-4 text-xs sm:text-sm font-black text-neutral-800 uppercase tracking-wider text-center group-hover:text-brand transition-colors duration-200 line-clamp-2 leading-snug">
                                {{ __($category->name) }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    {{-- Featured Products Slider Section --}}
    @if ($featuredProducts->isNotEmpty())
        @include('shop.partials.product-slider-section', [
            'title' => __('Featured Products'),
            'products' => $featuredProducts,
            'viewAll' => route('shop.index', ['featured' => 1])
        ])
    @endif

    {{-- Ladies Three Piece / Women's Products Section --}}
    @if ($ladiesThreePiece->isNotEmpty())
        @include('shop.partials.product-section', [
            'title' => __("Women's Products"),
            'products' => $ladiesThreePiece,
            'viewAll' => route('shop.index', ['category' => 'womens-products'])
        ])
    @endif

    {{-- Social Connect / Direct Engagement Section --}}
    <section class="shop-container mt-16">
        <!-- Centered Heading with Dividers -->
        <div class="flex items-center gap-4 mb-10">
            <div class="flex-grow border-t border-neutral-200/80"></div>
            <div class="text-center px-4">
                <span class="text-xs font-bold text-neutral-400 tracking-[0.2em] uppercase block mb-1">{{ __('Connect With Us') }}</span>
                <h2 class="text-xl sm:text-2xl font-bold text-neutral-800 tracking-wide uppercase">
                    {{ __('Explore & Order Directly') }}
                </h2>
            </div>
            <div class="flex-grow border-t border-neutral-200/80"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- WhatsApp Direct --}}
            <div class="group bg-white border border-neutral-100 hover:border-emerald-200 hover:shadow-xl hover:shadow-emerald-50/50 rounded-3xl p-6 sm:p-8 transition-all duration-300 flex flex-col justify-between items-start">
                <div class="w-full">
                    <div class="flex items-center justify-between mb-6">
                        <span class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 group-hover:shadow-md transition-all duration-300">
                            <i class="ph ph-whatsapp-logo text-2xl font-bold"></i>
                        </span>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full uppercase tracking-wider">{{ __('Active Chat') }}</span>
                    </div>
                    <h4 class="font-extrabold text-neutral-900 text-lg uppercase tracking-wide mb-3 group-hover:text-emerald-600 transition-colors duration-200">{{ __('Order Via WhatsApp') }}</h4>
                    <p class="text-neutral-500 text-xs sm:text-sm leading-relaxed mb-6">
                        {{ __('Have a question about sizes, fabrics, or customization? Want to place your order directly? Chat with our styling assistants.') }}
                    </p>
                </div>
                <a href="https://wa.me/8801710733329" target="_blank" rel="noopener"
                    class="w-full border border-neutral-200 hover:border-emerald-500 hover:bg-emerald-500 hover:text-white text-neutral-800 font-bold py-3 px-6 rounded-xl transition-all duration-300 text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                    <i class="ph ph-chat-circle text-base"></i>
                    <span>{{ __('Start WhatsApp Chat') }}</span>
                </a>
            </div>

            {{-- Facebook Community --}}
            <div class="group bg-white border border-neutral-100 hover:border-blue-200 hover:shadow-xl hover:shadow-blue-50/50 rounded-3xl p-6 sm:p-8 transition-all duration-300 flex flex-col justify-between items-start">
                <div class="w-full">
                    <div class="flex items-center justify-between mb-6">
                        <span class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 group-hover:shadow-md transition-all duration-300">
                            <i class="ph ph-facebook-logo text-2xl font-bold"></i>
                        </span>
                        <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full uppercase tracking-wider">{{ __('Live Updates') }}</span>
                    </div>
                    <h4 class="font-extrabold text-neutral-900 text-lg uppercase tracking-wide mb-3 group-hover:text-blue-600 transition-colors duration-200">{{ __('Facebook Page') }}</h4>
                    <p class="text-neutral-500 text-xs sm:text-sm leading-relaxed mb-6">
                        {{ __('Explore daily styling inspirations, customer reviews, photo albums, and join our regular interactive live shows.') }}
                    </p>
                </div>
                @php
                    $fbLink = collect(config('application_info.social_medias'))->firstWhere('name', 'Facebook')['link'] ?? 'https://facebook.com';
                @endphp
                <a href="{{ $fbLink }}" target="_blank" rel="noopener"
                    class="w-full border border-neutral-200 hover:border-blue-500 hover:bg-blue-500 hover:text-white text-neutral-800 font-bold py-3 px-6 rounded-xl transition-all duration-300 text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                    <i class="ph ph-thumbs-up text-base"></i>
                    <span>{{ __('Visit Facebook Page') }}</span>
                </a>
            </div>

            {{-- Direct Call --}}
            <div class="group bg-white border border-neutral-100 hover:border-brand/30 hover:shadow-xl hover:shadow-brand/5 rounded-3xl p-6 sm:p-8 transition-all duration-300 flex flex-col justify-between items-start">
                <div class="w-full">
                    <div class="flex items-center justify-between mb-6">
                        <span class="w-12 h-12 rounded-2xl bg-brand/5 text-brand flex items-center justify-center group-hover:scale-110 group-hover:shadow-md transition-all duration-300">
                            <i class="ph ph-phone text-2xl font-bold"></i>
                        </span>
                        <span class="text-[10px] font-bold text-brand bg-brand/5 px-2.5 py-1 rounded-full uppercase tracking-wider">{{ __('Hotline') }}</span>
                    </div>
                    <h4 class="font-extrabold text-neutral-900 text-lg uppercase tracking-wide mb-3 group-hover:text-brand transition-colors duration-200">{{ __('Call Style Experts') }}</h4>
                    <p class="text-neutral-500 text-xs sm:text-sm leading-relaxed mb-6">
                        {{ __('Prefer to speak directly with an assistant? Call our help hotline for instant support with your orders, sizing, or deliveries.') }}
                    </p>
                </div>
                <a href="tel:01935100013"
                    class="w-full border border-neutral-200 hover:border-brand hover:bg-brand hover:text-white text-neutral-800 font-bold py-3 px-6 rounded-xl transition-all duration-300 text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                    <i class="ph ph-phone-call text-base"></i>
                    <span>{{ __('Call Support Hotline') }}</span>
                </a>
            </div>
        </div>
    </section>



@endsection
