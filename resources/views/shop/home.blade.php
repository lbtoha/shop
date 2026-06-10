@extends('shop.layouts.app')

@section('title', config('application_info.company_info.name') . ' — ' . __('Premium Ethnic Wear'))

@section('content')
    {{-- Full-Width Hero Slider --}}
    @if ($banners->isNotEmpty())
        <section class="w-full relative overflow-hidden h-[250px] sm:h-[420px] lg:h-[520px] bg-neutral-100" data-hero>
            @foreach ($banners as $i => $banner)
                <div data-slide class="{{ $i === 0 ? '' : 'hidden' }} absolute inset-0 w-full h-full">
                    <a href="{{ $banner->link ?: route('shop.index') }}" class="block w-full h-full">
                        <img src="{{ $banner->image }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                    </a>
                </div>
            @endforeach
        </section>
    @endif

    {{-- Main Container --}}
    <div class="shop-container">
        {{-- Info Strip / Feature List --}}
        <section class="mt-8 grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $features = [
                    ['icon' => 'ph-truck', 'title' => __('Free Delivery'), 'text' => __('Orders above ৳5000')],
                    ['icon' => 'ph-arrow-counter-clockwise', 'title' => __('Easy Returns'), 'text' => __('7 days exchange policy')],
                    ['icon' => 'ph-hand-coins', 'title' => __('Cash on Delivery'), 'text' => __('Pay at your doorstep')],
                    ['icon' => 'ph-shield-check', 'title' => __('Safe Shopping'), 'text' => __('100% genuine products')],
                ];
            @endphp
            @foreach ($features as $f)
                <div class="bg-white border border-neutral-100 rounded-2xl p-4 flex items-center gap-3.5 shadow-sm transition hover:shadow-md">
                    <div class="w-10 h-10 rounded-full bg-brand/5 flex items-center justify-center shrink-0">
                        <i class="ph {{ $f['icon'] }} text-xl text-brand"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs sm:text-sm font-bold text-neutral-800 truncate">{{ $f['title'] }}</div>
                        <div class="text-[10px] sm:text-xs text-neutral-400 font-medium mt-0.5 truncate">{{ $f['text'] }}</div>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- Shop By Category Section --}}
        @if ($categories->isNotEmpty())
            <section class="mt-16">
                <div class="relative mb-8 border-b border-neutral-100 pb-3">
                    <h2 class="text-xl sm:text-2xl font-black text-neutral-900 tracking-wider uppercase inline-block relative">
                        {{ __('SHOP BY CATEGORY') }}
                        <span class="absolute bottom-[-13px] left-0 w-16 h-0.5 bg-brand"></span>
                    </h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($categories as $category)
                        <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                            class="group relative h-[180px] sm:h-[220px] rounded-3xl overflow-hidden shadow-sm block">
                            <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                            <div class="absolute bottom-6 left-6">
                                <h3 class="text-white text-lg sm:text-xl font-black tracking-wider uppercase">
                                    {{ __($category->name) }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    {{-- New Arrivals Product Section --}}
    @include('shop.partials.product-section', [
        'title' => __('New Arrivals'),
        'products' => $newCollection,
        'viewAll' => route('shop.index', ['sort' => 'newest'])
    ])

    {{-- Hot Sale Product Section --}}
    @if ($hotSale->isNotEmpty())
        @include('shop.partials.product-section', [
            'title' => __('Hot Sale'),
            'products' => $hotSale,
            'viewAll' => route('shop.index')
        ])
    @endif

    {{-- Ladies Three Piece Section --}}
    @if ($ladiesThreePiece->isNotEmpty())
        @include('shop.partials.product-section', [
            'title' => __('Ladies Three Piece'),
            'products' => $ladiesThreePiece,
            'viewAll' => route('shop.index', ['category' => 'ladies-three-piece'])
        ])
    @endif

    {{-- Videos Section --}}
    <section class="shop-container mt-16">
        <div class="relative mb-8 border-b border-neutral-100 pb-3">
            <h2 class="text-xl sm:text-2xl font-black text-neutral-900 tracking-wider uppercase inline-block relative">
                {{ __('VIDEOS') }}
                <span class="absolute bottom-[-13px] left-0 w-16 h-0.5 bg-brand"></span>
            </h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @php
                $videoThumbs = [
                    'https://images.unsplash.com/photo-1608748010899-18f300247112?w=400&q=80',
                    'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=400&q=80',
                    'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?w=400&q=80',
                    'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=400&q=80',
                    'https://images.unsplash.com/photo-1609357605129-26f69add5d6e?w=400&q=80',
                ];
            @endphp
            @foreach ($videoThumbs as $thumb)
                <div class="group relative rounded-2xl overflow-hidden aspect-[9/16] shadow-sm cursor-pointer bg-neutral-100">
                    <img src="{{ $thumb }}" alt="Video" class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                    <div class="absolute inset-0 bg-black/30 flex items-center justify-center transition duration-300 group-hover:bg-black/40">
                        <div class="w-12 h-12 rounded-full bg-white/90 text-neutral-900 flex items-center justify-center transition-transform duration-300 group-hover:scale-110 shadow-md">
                            <i class="ph ph-play text-xl fill-current"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Showrooms Section --}}
    <section class="mt-16 mb-12 shop-container">
        <div class="text-center mb-8">
            <h2 class="text-xs font-bold text-neutral-400 tracking-[0.2em] uppercase">{{ __('VISIT US IN PERSON') }}</h2>
            <h3 class="text-2xl sm:text-3xl font-black text-neutral-900 mt-2 uppercase tracking-wide">{{ __('FIND OUR SHOWROOMS') }}</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            {{-- Store 1 --}}
            <div class="bg-white border border-neutral-100 rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col justify-between items-start">
                <div>
                    <div class="flex items-center gap-2 text-brand mb-4">
                        <i class="ph ph-map-pin text-2xl font-bold"></i>
                        <h4 class="font-extrabold text-neutral-900 text-lg uppercase tracking-wide">{{ __('Uttara 10 Flagship Store') }}</h4>
                    </div>
                    <p class="text-neutral-500 text-sm leading-relaxed mb-6">
                        {{ __('House #23, Road #12, Sector 10, Uttara, Dhaka-1230, Bangladesh.') }}<br>
                        {{ __('Phone: +880 1711223344') }}
                    </p>
                </div>
                <a href="https://maps.google.com" target="_blank"
                    class="inline-flex items-center gap-2 border-2 border-neutral-200 hover:border-brand hover:text-brand text-neutral-800 font-bold px-5 py-2.5 rounded-xl transition text-xs tracking-wider uppercase">
                    <i class="ph ph-compass"></i>
                    <span>{{ __('VIEW ON MAP') }}</span>
                </a>
            </div>
            {{-- Store 2 --}}
            <div class="bg-white border border-neutral-100 rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col justify-between items-start">
                <div>
                    <div class="flex items-center gap-2 text-brand mb-4">
                        <i class="ph ph-map-pin text-2xl font-bold"></i>
                        <h4 class="font-extrabold text-neutral-900 text-lg uppercase tracking-wide">{{ __('Uttara 13 Outlet') }}</h4>
                    </div>
                    <p class="text-neutral-500 text-sm leading-relaxed mb-6">
                        {{ __('Flat #4A, House #45, Sector 13, Sonargaon Janapath Road, Uttara, Dhaka-1230, Bangladesh.') }}<br>
                        {{ __('Phone: +880 1711223355') }}
                    </p>
                </div>
                <a href="https://maps.google.com" target="_blank"
                    class="inline-flex items-center gap-2 border-2 border-neutral-200 hover:border-brand hover:text-brand text-neutral-800 font-bold px-5 py-2.5 rounded-xl transition text-xs tracking-wider uppercase">
                    <i class="ph ph-compass"></i>
                    <span>{{ __('VIEW ON MAP') }}</span>
                </a>
            </div>
        </div>
    </section>
@endsection
