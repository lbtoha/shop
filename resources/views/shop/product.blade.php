@extends('shop.layouts.app')

@section('title', $product->name . ' — ' . config('application_info.company_info.name'))

@section('content')
    @php
        $hasDiscount = $product->compare_at_price && $product->compare_at_price > $product->price;
        $gallery = $product->images->pluck('image')->filter()->values();
        if ($product->thumbnail) {
            $gallery->prepend($product->thumbnail);
        }
        $mainImage = $gallery->first();
    @endphp

    <div class="max-w-7xl mx-auto px-4 py-8">
        <nav class="text-xs text-[color:var(--color-muted)] mb-5">
            <a href="{{ route('home') }}" class="hover:text-[color:var(--color-brand)]">{{ __('Home') }}</a>
            <span class="mx-1">/</span>
            <a href="{{ route('shop.index') }}" class="hover:text-[color:var(--color-brand)]">{{ __('Shop') }}</a>
            <span class="mx-1">/</span><span>{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            {{-- Gallery --}}
            <div>
                <div class="aspect-square bg-white border border-neutral-100 overflow-hidden flex items-center justify-center">
                    @if ($mainImage)
                        <img id="main-product-image" src="{{ $mainImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <i class="ph ph-image text-7xl text-neutral-300"></i>
                    @endif
                </div>
                @if ($gallery->count() > 1)
                    <div class="flex gap-2 mt-3">
                        @foreach ($gallery as $img)
                            <button type="button"
                                onclick="document.getElementById('main-product-image').src='{{ $img }}'"
                                class="w-16 h-16 border border-neutral-200 overflow-hidden hover:border-[color:var(--color-brand)]">
                                <img src="{{ $img }}" alt="" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div>
                @if ($product->category)
                    <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}"
                        class="text-xs uppercase tracking-wide text-[color:var(--color-brand)]">{{ $product->category->name }}</a>
                @endif
                <h1 class="text-2xl sm:text-3xl font-bold text-ink mt-1">{{ $product->name }}</h1>

                <div class="mt-4 flex items-center gap-3">
                    <span class="text-2xl font-bold text-[color:var(--color-brand)]">{{ amountWithSymbol($product->price) }}</span>
                    @if ($hasDiscount)
                        <span class="text-lg text-neutral-400 line-through">{{ amountWithSymbol($product->compare_at_price) }}</span>
                    @endif
                </div>

                <div class="mt-2">
                    @if ($product->isInStock())
                        <span class="inline-flex items-center gap-1 text-sm text-emerald-600"><i class="ph ph-check-circle"></i> {{ __('In stock') }} ({{ $product->stock }})</span>
                    @else
                        <span class="inline-flex items-center gap-1 text-sm text-red-600"><i class="ph ph-x-circle"></i> {{ __('Out of stock') }}</span>
                    @endif
                </div>

                @if ($product->short_description)
                    <p class="mt-4 text-[color:var(--color-muted)] leading-relaxed">{{ $product->short_description }}</p>
                @endif

                @if ($product->isInStock())
                    <div class="mt-6 flex items-center gap-4">
                        <div data-qty-wrap class="flex items-center border border-neutral-200 rounded">
                            <button type="button" data-step="-1" class="px-3 py-2 text-lg hover:bg-neutral-50">−</button>
                            <input type="number" data-quantity-input value="1" min="1" max="{{ $product->stock }}"
                                class="w-14 text-center border-x border-neutral-200 py-2 focus:outline-none">
                            <button type="button" data-step="1" class="px-3 py-2 text-lg hover:bg-neutral-50">+</button>
                        </div>
                        <button type="button" data-add-to-cart="{{ route('shop.cart.add', $product->id) }}"
                            class="flex-1 bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white font-medium py-3 rounded transition-colors">
                            <i class="ph ph-shopping-cart-simple"></i> {{ __('Add to Cart') }}
                        </button>
                    </div>
                @endif

                <div class="mt-6 flex items-center gap-2 text-sm bg-white border border-neutral-100 rounded px-4 py-3">
                    <i class="ph ph-truck text-[color:var(--color-brand)] text-xl"></i>
                    {{ __('Cash on Delivery available') }}
                </div>

                @if ($product->description)
                    <div class="mt-8">
                        <h3 class="font-semibold mb-2 text-ink">{{ __('Description') }}</h3>
                        <div class="prose prose-sm max-w-none text-[color:var(--color-muted)]">
                            {!! $product->description !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if ($related->isNotEmpty())
            <section class="mt-16">
                <h2 class="text-xl font-bold text-ink mb-5">{{ __('Related Products') }}</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($related as $item)
                        <x-shop::product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
