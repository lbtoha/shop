@extends('shop.layouts.app')

@section('title', config('application_info.company_info.name') . ' — ' . __('Shop'))

@section('content')
    {{-- Hero carousel: admin-managed banners, with a static fallback when none exist --}}
    @php
        $gradients = ['from-[#8a5a44] to-[#b08968]', 'from-[#6f4636] to-[#8a5a44]', 'from-[#b08968] to-[#6f4636]'];
        $heroSlides = $banners->isNotEmpty()
            ? $banners
            : collect([
                (object) ['title' => __('New Collection 2026'), 'subtitle' => __('Discover the latest arrivals'), 'image' => null, 'button_text' => __('Shop Now'), 'link' => route('shop.index')],
                (object) ['title' => __('Exclusive Trends'), 'subtitle' => __('Hand-picked styles for you'), 'image' => null, 'button_text' => __('Explore'), 'link' => route('shop.index')],
                (object) ['title' => __('Cash on Delivery'), 'subtitle' => __('Order now, pay when it arrives'), 'image' => null, 'button_text' => __('Start Shopping'), 'link' => route('shop.index')],
            ]);
    @endphp
    <section data-hero class="relative max-w-7xl mx-auto px-4 mt-4">
        @foreach ($heroSlides as $i => $slide)
            <div data-slide class="{{ $i === 0 ? '' : 'hidden' }} rounded-lg overflow-hidden">
                @if ($slide->image)
                    <a href="{{ $slide->link ?: route('shop.index') }}" class="block relative h-56 sm:h-80">
                        <img src="{{ $slide->image }}" alt="{{ $slide->title }}" class="w-full h-full object-cover">
                        @if ($slide->title || $slide->subtitle)
                            <div class="absolute inset-0 bg-black/30 flex flex-col items-center justify-center text-center text-white px-4">
                                @if ($slide->title)<h2 class="text-2xl sm:text-4xl font-bold">{{ $slide->title }}</h2>@endif
                                @if ($slide->subtitle)<p class="mt-2 text-sm sm:text-lg opacity-90">{{ $slide->subtitle }}</p>@endif
                                @if ($slide->button_text)
                                    <span class="mt-5 inline-block bg-white text-[color:var(--color-brand)] font-semibold px-6 py-2.5 rounded-full">{{ $slide->button_text }}</span>
                                @endif
                            </div>
                        @endif
                    </a>
                @else
                    <div class="bg-gradient-to-r {{ $gradients[$i % count($gradients)] }} h-56 sm:h-80 flex flex-col items-center justify-center text-center text-white px-4">
                        <h2 class="text-2xl sm:text-4xl font-bold">{{ $slide->title }}</h2>
                        <p class="mt-2 text-sm sm:text-lg opacity-90">{{ $slide->subtitle }}</p>
                        <a href="{{ $slide->link ?: route('shop.index') }}"
                            class="mt-5 inline-block bg-white text-[color:var(--color-brand)] font-semibold px-6 py-2.5 rounded-full hover:bg-neutral-100 transition">
                            {{ $slide->button_text ?: __('Shop Now') }}
                        </a>
                    </div>
                @endif
            </div>
        @endforeach
    </section>

    {{-- Category strip --}}
    @if ($categories->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 mt-10">
            <div class="flex gap-4 overflow-x-auto no-scrollbar pb-2">
                @foreach ($categories as $category)
                    <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                        class="shrink-0 flex flex-col items-center gap-2 w-24 group">
                        <div class="w-20 h-20 rounded-full overflow-hidden bg-white border border-neutral-100 shadow-sm flex items-center justify-center">
                            @if ($category->image)
                                <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="ph ph-tag text-2xl text-[color:var(--color-brand-light)]"></i>
                            @endif
                        </div>
                        <span class="text-xs text-center text-ink group-hover:text-[color:var(--color-brand)] line-clamp-1">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @include('shop.partials.product-section', ['title' => __('New Collection'), 'products' => $newCollection, 'viewAll' => route('shop.index', ['sort' => 'newest'])])

    @if ($hotSale->isNotEmpty())
        @include('shop.partials.product-section', ['title' => __('Hot Sale'), 'products' => $hotSale, 'viewAll' => route('shop.index')])
    @endif

    @if ($featured->isNotEmpty())
        @include('shop.partials.product-section', ['title' => __('Featured'), 'products' => $featured, 'viewAll' => route('shop.index')])
    @endif
@endsection
