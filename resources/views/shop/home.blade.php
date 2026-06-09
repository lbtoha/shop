@extends('shop.layouts.app')

@section('title', config('application_info.company_info.name') . ' — ' . __('Shop'))

@section('content')
    <div class="max-w-7xl mx-auto px-4">
        {{-- Hero + side promos --}}
        @php
            $gradients = ['from-[#005c08] to-[#00b207]', 'from-[#1a1a1a] to-[#2c742f]', 'from-[#00b207] to-[#84d187]'];
            $heroSlides = $banners->isNotEmpty()
                ? $banners
                : collect([
                    (object) ['title' => __('Everyday Fresh & Best Deals'), 'subtitle' => __('Shop the new collection — pay on delivery'), 'image' => null, 'button_text' => __('Shop Now'), 'link' => route('shop.index')],
                    (object) ['title' => __('Up to 50% Off'), 'subtitle' => __('Hand-picked styles for limited time'), 'image' => null, 'button_text' => __('Grab Deals'), 'link' => route('shop.index')],
                    (object) ['title' => __('Cash on Delivery'), 'subtitle' => __('Order now, pay when it arrives'), 'image' => null, 'button_text' => __('Start Shopping'), 'link' => route('shop.index')],
                ]);
        @endphp
        <section class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Hero carousel --}}
            <div data-hero class="lg:col-span-2 relative rounded-xl overflow-hidden">
                @foreach ($heroSlides as $i => $slide)
                    <div data-slide class="{{ $i === 0 ? '' : 'hidden' }}">
                        @if ($slide->image)
                            <a href="{{ $slide->link ?: route('shop.index') }}" class="block relative h-72 sm:h-[420px]">
                                <img src="{{ $slide->image }}" alt="{{ $slide->title }}" class="w-full h-full object-cover">
                                @if ($slide->title || $slide->subtitle)
                                    <div class="absolute inset-0 bg-gradient-to-r from-black/55 to-transparent flex flex-col justify-center px-8 sm:px-12 max-w-lg">
                                        @if ($slide->title)<h2 class="text-3xl sm:text-5xl font-extrabold text-white leading-tight">{{ $slide->title }}</h2>@endif
                                        @if ($slide->subtitle)<p class="mt-3 text-white/90 sm:text-lg">{{ $slide->subtitle }}</p>@endif
                                        @if ($slide->button_text)
                                            <span class="mt-6 inline-flex w-max items-center gap-2 bg-[color:var(--color-brand)] text-white font-semibold px-6 py-3 rounded-full">{{ $slide->button_text }} <i class="ph ph-arrow-right"></i></span>
                                        @endif
                                    </div>
                                @endif
                            </a>
                        @else
                            <div class="relative h-72 sm:h-[420px] bg-gradient-to-r {{ $gradients[$i % count($gradients)] }} flex flex-col justify-center px-8 sm:px-12">
                                <span class="text-white/80 text-sm font-medium uppercase tracking-wider">{{ __('Welcome to') }} {{ config('application_info.company_info.name') }}</span>
                                <h2 class="mt-2 text-3xl sm:text-5xl font-extrabold text-white leading-tight max-w-md">{{ $slide->title }}</h2>
                                <p class="mt-3 text-white/90 sm:text-lg max-w-md">{{ $slide->subtitle }}</p>
                                <a href="{{ $slide->link ?: route('shop.index') }}"
                                    class="mt-6 inline-flex w-max items-center gap-2 bg-white text-[color:var(--color-brand-dark)] font-semibold px-6 py-3 rounded-full hover:bg-neutral-100 transition">
                                    {{ $slide->button_text ?: __('Shop Now') }} <i class="ph ph-arrow-right"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Side promo cards --}}
            <div class="hidden lg:flex flex-col gap-5">
                <a href="{{ route('shop.index', ['sort' => 'newest']) }}" class="flex-1 rounded-xl bg-[color:var(--color-brand-soft)] p-6 flex flex-col justify-center relative overflow-hidden group">
                    <span class="text-xs font-semibold text-[color:var(--color-brand-dark)] uppercase">{{ __('New Arrivals') }}</span>
                    <h3 class="mt-1 text-xl font-bold text-[color:var(--color-ink)] max-w-[60%]">{{ __('Fresh styles, just in') }}</h3>
                    <span class="mt-3 text-sm font-medium text-[color:var(--color-brand-dark)] group-hover:underline">{{ __('Shop now') }} →</span>
                </a>
                <a href="{{ route('shop.index') }}" class="flex-1 rounded-xl bg-[color:var(--color-ink)] p-6 flex flex-col justify-center relative overflow-hidden group">
                    <span class="text-xs font-semibold text-[color:var(--color-brand-light)] uppercase">{{ __('Cash on Delivery') }}</span>
                    <h3 class="mt-1 text-xl font-bold text-white max-w-[70%]">{{ __('Pay when it arrives') }}</h3>
                    <span class="mt-3 text-sm font-medium text-[color:var(--color-brand-light)] group-hover:underline">{{ __('Browse all') }} →</span>
                </a>
            </div>
        </section>

        {{-- Feature strip --}}
        <section class="mt-6 grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $features = [
                    ['icon' => 'ph-truck', 'title' => __('Cash on Delivery'), 'text' => __('Pay at your door')],
                    ['icon' => 'ph-arrow-counter-clockwise', 'title' => __('Easy Returns'), 'text' => __('Hassle-free policy')],
                    ['icon' => 'ph-shield-check', 'title' => __('Secure Checkout'), 'text' => __('Your data is safe')],
                    ['icon' => 'ph-headset', 'title' => __('Support'), 'text' => __('We are here to help')],
                ];
            @endphp
            @foreach ($features as $f)
                <div class="bg-white border border-[color:var(--color-line)] rounded-lg p-4 flex items-center gap-3">
                    <i class="ph {{ $f['icon'] }} text-3xl text-[color:var(--color-brand)]"></i>
                    <div>
                        <div class="text-sm font-semibold text-[color:var(--color-ink)]">{{ $f['title'] }}</div>
                        <div class="text-xs text-[color:var(--color-muted)]">{{ $f['text'] }}</div>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- Category showcase --}}
        @if ($categories->isNotEmpty())
            <section class="mt-12">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl sm:text-2xl font-bold text-[color:var(--color-ink)]">{{ __('Shop by Category') }}</h2>
                    <a href="{{ route('shop.index') }}" class="text-sm font-medium text-[color:var(--color-brand)] hover:underline">{{ __('View All') }} <i class="ph ph-arrow-right"></i></a>
                </div>
                <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach ($categories as $category)
                        <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                            class="group bg-white border border-[color:var(--color-line)] rounded-lg p-4 flex flex-col items-center gap-3 hover:border-[color:var(--color-brand)] hover:shadow-sm transition">
                            <div class="w-16 h-16 rounded-full overflow-hidden bg-[color:var(--color-brand-soft)] flex items-center justify-center">
                                @if ($category->image)
                                    <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                                @else
                                    <i class="ph ph-package text-2xl text-[color:var(--color-brand)]"></i>
                                @endif
                            </div>
                            <span class="text-xs text-center font-medium text-[color:var(--color-ink)] group-hover:text-[color:var(--color-brand)] line-clamp-1">{{ $category->name }}</span>
                            <span class="text-[11px] text-[color:var(--color-muted)]">{{ $category->products_count }} {{ __('items') }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    @include('shop.partials.product-section', ['title' => __('New Collection'), 'subtitle' => __('Fresh picks added recently'), 'products' => $newCollection, 'viewAll' => route('shop.index', ['sort' => 'newest'])])

    @if ($hotSale->isNotEmpty())
        {{-- Promo banner between sections --}}
        <div class="max-w-7xl mx-auto px-4 mt-12">
            <div class="rounded-xl bg-gradient-to-r from-[color:var(--color-ink)] to-[color:var(--color-brand-dark)] px-8 py-10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="inline-block bg-[color:var(--color-accent)] text-white text-xs font-semibold px-2.5 py-1 rounded">🔥 {{ __('Limited time') }}</span>
                    <h3 class="mt-3 text-2xl sm:text-3xl font-extrabold text-white">{{ __('Hot deals up to 50% off') }}</h3>
                    <p class="mt-1 text-white/80">{{ __('Grab your favorites before they sell out') }}</p>
                </div>
                <a href="{{ route('shop.index') }}" class="bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-light)] text-white font-semibold px-6 py-3 rounded-full whitespace-nowrap">{{ __('Shop Deals') }}</a>
            </div>
        </div>
        @include('shop.partials.product-section', ['title' => __('Hot Sale'), 'subtitle' => __('Discounted right now'), 'products' => $hotSale, 'viewAll' => route('shop.index')])
    @endif

    @if ($featured->isNotEmpty())
        @include('shop.partials.product-section', ['title' => __('Featured Products'), 'subtitle' => __('Our top recommendations'), 'products' => $featured, 'viewAll' => route('shop.index')])
    @endif

    <div class="h-4"></div>
@endsection
