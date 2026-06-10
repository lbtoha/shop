@props(['product'])

@php
    $hasDiscount = $product->compare_at_price && $product->compare_at_price > $product->price;
    $discountPercent = $hasDiscount
        ? round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100)
        : 0;
    $primaryImage = $product->thumbnail ?: ($product->relationLoaded('images') ? optional($product->images->first())->image : null);
    $isNew = $product->created_at && $product->created_at->gt(now()->subDays(14));
@endphp

<div class="product-card group bg-white border border-neutral-100 rounded-2xl p-3 hover:shadow-[0_12px_24px_rgba(0,0,0,0.04)] transition-all duration-300 flex flex-col justify-between">
    <div>
        <div class="relative overflow-hidden rounded-xl bg-[color:var(--color-image)] aspect-[3/4]">
            {{-- Badges --}}
            <div class="absolute top-2 left-2 z-10 flex flex-col gap-1">
                @if ($hasDiscount)
                    <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">-{{ $discountPercent }}%</span>
                @endif
                @if ($isNew)
                    <span class="bg-brand text-white text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">{{ __('NEW') }}</span>
                @endif
                @if (! $product->isInStock())
                    <span class="bg-neutral-800 text-white text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">{{ __('SOLD OUT') }}</span>
                @endif
            </div>

            {{-- Image --}}
            <a href="{{ route('shop.product', $product->slug) }}" class="pc-image block w-full h-full">
                @if ($primaryImage)
                    <img src="{{ $primaryImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                @else
                    <div class="w-full h-full flex items-center justify-center text-neutral-300"><i class="ph ph-image text-5xl"></i></div>
                @endif
            </a>
        </div>

        {{-- Content --}}
        <div class="pt-4 pb-2 text-center">
            <h5 class="text-xs sm:text-sm font-semibold text-neutral-800 line-clamp-2 min-h-[2.5rem] px-1 hover:text-brand transition-colors">
                <a href="{{ route('shop.product', $product->slug) }}">{{ $product->name }}</a>
            </h5>

            <div class="mt-2 flex items-center justify-center gap-2">
                <span class="text-sm sm:text-base font-bold text-brand">{{ amountWithSymbol($product->price) }}</span>
                @if ($hasDiscount)
                    <span class="text-xs text-neutral-400 line-through">{{ amountWithSymbol($product->compare_at_price) }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Full width Add to Cart / Choose Options Button --}}
    <div class="mt-2">
        @if ($product->isInStock())
            @if ($product->hasVariants())
                <a href="{{ route('shop.product', $product->slug) }}"
                    class="w-full bg-neutral-900 hover:bg-neutral-800 text-white font-bold py-2.5 px-3 rounded-lg transition duration-200 text-xs tracking-wider uppercase flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="ph ph-sliders-horizontal text-sm"></i>
                    <span>{{ __('CHOOSE OPTIONS') }}</span>
                </a>
            @else
                <button type="button" data-add-to-cart="{{ route('shop.cart.add', $product->id) }}"
                    class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-2.5 px-3 rounded-lg transition duration-200 text-xs tracking-wider uppercase flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="ph ph-shopping-cart-simple text-sm"></i>
                    <span>{{ __('ADD TO CART') }}</span>
                </button>
            @endif
        @else
            <button type="button" disabled
                class="w-full bg-neutral-100 text-neutral-400 font-bold py-2.5 px-3 rounded-lg text-xs tracking-wider uppercase flex items-center justify-center gap-1.5 cursor-not-allowed">
                <span>{{ __('SOLD OUT') }}</span>
            </button>
        @endif
    </div>
</div>
