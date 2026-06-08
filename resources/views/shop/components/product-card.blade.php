@props(['product'])

@php
    $hasDiscount = $product->compare_at_price && $product->compare_at_price > $product->price;
    $discountPercent = $hasDiscount
        ? round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100)
        : 0;
    $primaryImage = $product->thumbnail ?: ($product->relationLoaded('images') ? optional($product->images->first())->image : null);
@endphp

<div class="product-card group bg-white border border-neutral-100 shadow-sm hover:shadow-md transition-shadow flex flex-col">
    <a href="{{ route('shop.product', $product->slug) }}" class="relative block aspect-square overflow-hidden bg-neutral-50">
        @if ($primaryImage)
            <img src="{{ $primaryImage }}" alt="{{ $product->name }}"
                class="w-full h-full object-cover" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center text-neutral-300">
                <i class="ph ph-image text-5xl"></i>
            </div>
        @endif

        @if ($hasDiscount)
            <span class="absolute top-2 left-2 bg-[color:var(--color-brand)] text-white text-xs font-semibold px-2 py-1 rounded">
                -{{ $discountPercent }}%
            </span>
        @endif
        @if (! $product->isInStock())
            <span class="absolute top-2 right-2 bg-neutral-700 text-white text-xs px-2 py-1 rounded">
                {{ __('Out of stock') }}
            </span>
        @endif
    </a>

    <div class="p-3 flex flex-col flex-1">
        @if ($product->category)
            <span class="text-[11px] uppercase tracking-wide text-[color:var(--color-muted)]">{{ $product->category->name }}</span>
        @endif
        <a href="{{ route('shop.product', $product->slug) }}"
            class="text-sm font-medium text-ink line-clamp-2 hover:text-[color:var(--color-brand)] mt-0.5 min-h-[2.5rem]">
            {{ $product->name }}
        </a>

        <div class="mt-2 flex items-center gap-2">
            <span class="text-[color:var(--color-brand)] font-semibold">{{ amountWithSymbol($product->price) }}</span>
            @if ($hasDiscount)
                <span class="text-xs text-neutral-400 line-through">{{ amountWithSymbol($product->compare_at_price) }}</span>
            @endif
        </div>

        <div class="mt-3 flex-1 flex items-end">
            @if ($product->isInStock())
                <button type="button" data-add-to-cart="{{ route('shop.cart.add', $product->id) }}"
                    class="w-full bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white text-sm font-medium py-2 rounded transition-colors">
                    {{ __('Order Now') }}
                </button>
            @else
                <button type="button" disabled
                    class="w-full bg-neutral-200 text-neutral-500 text-sm font-medium py-2 rounded cursor-not-allowed">
                    {{ __('Sold Out') }}
                </button>
            @endif
        </div>
    </div>
</div>
