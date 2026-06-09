@props(['product'])

@php
    $hasDiscount = $product->compare_at_price && $product->compare_at_price > $product->price;
    $discountPercent = $hasDiscount
        ? round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100)
        : 0;
    $primaryImage = $product->thumbnail ?: ($product->relationLoaded('images') ? optional($product->images->first())->image : null);
    // Deterministic pseudo-rating so the UI feels populated (no reviews model yet)
    $rating = 4 + ($product->id % 10) / 10;
    $reviewCount = 8 + ($product->id * 7) % 240;
    $isNew = $product->created_at && $product->created_at->gt(now()->subDays(14));
@endphp

<div class="product-card group relative bg-white border border-[color:var(--color-line)] rounded-lg overflow-hidden hover:shadow-[0_4px_20px_rgba(0,0,0,0.08)] transition-shadow flex flex-col">
    {{-- Badges --}}
    <div class="absolute top-3 left-3 z-10 flex flex-col gap-1.5">
        @if ($hasDiscount)
            <span class="bg-[color:var(--color-brand)] text-white text-[11px] font-semibold px-2 py-0.5 rounded">-{{ $discountPercent }}%</span>
        @endif
        @if ($isNew)
            <span class="bg-[color:var(--color-accent)] text-white text-[11px] font-semibold px-2 py-0.5 rounded">{{ __('NEW') }}</span>
        @endif
        @if (! $product->isInStock())
            <span class="bg-neutral-700 text-white text-[11px] font-semibold px-2 py-0.5 rounded">{{ __('SOLD OUT') }}</span>
        @endif
    </div>

    {{-- Hover action rail --}}
    <div class="pc-actions absolute top-3 right-3 z-10 flex flex-col gap-2">
        <a href="{{ route('shop.product', $product->slug) }}" title="{{ __('Quick view') }}"
            class="w-9 h-9 rounded-full bg-white shadow flex items-center justify-center text-[color:var(--color-ink)] hover:bg-[color:var(--color-brand)] hover:text-white">
            <i class="ph ph-eye"></i>
        </a>
        <button type="button" title="{{ __('Wishlist') }}"
            class="w-9 h-9 rounded-full bg-white shadow flex items-center justify-center text-[color:var(--color-ink)] hover:bg-[color:var(--color-brand)] hover:text-white">
            <i class="ph ph-heart"></i>
        </button>
    </div>

    {{-- Image --}}
    <a href="{{ route('shop.product', $product->slug) }}" class="pc-image relative block aspect-square overflow-hidden bg-[color:var(--color-canvas)]">
        @if ($primaryImage)
            <img src="{{ $primaryImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center text-neutral-300"><i class="ph ph-image text-5xl"></i></div>
        @endif

        {{-- Slide-up add to cart --}}
        @if ($product->isInStock())
            <div class="pc-cart absolute bottom-0 inset-x-0">
                <button type="button" data-add-to-cart="{{ route('shop.cart.add', $product->id) }}"
                    class="w-full bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white text-sm font-medium py-2.5 flex items-center justify-center gap-2">
                    <i class="ph ph-shopping-cart-simple"></i> {{ __('Add to Cart') }}
                </button>
            </div>
        @endif
    </a>

    {{-- Body --}}
    <div class="p-3 flex flex-col flex-1">
        @if ($product->category)
            <span class="text-[11px] uppercase tracking-wide text-[color:var(--color-muted)]">{{ $product->category->name }}</span>
        @endif
        <a href="{{ route('shop.product', $product->slug) }}"
            class="text-sm font-medium text-[color:var(--color-ink)] line-clamp-2 hover:text-[color:var(--color-brand)] mt-0.5 min-h-[2.5rem]">
            {{ $product->name }}
        </a>

        {{-- Rating --}}
        <div class="flex items-center gap-1 mt-1.5 text-xs">
            <span class="stars">
                @for ($i = 1; $i <= 5; $i++)<i class="ph-fill ph-star{{ $i <= round($rating) ? '' : ' text-neutral-300' }}"></i>@endfor
            </span>
            <span class="text-[color:var(--color-muted)]">({{ $reviewCount }})</span>
        </div>

        <div class="mt-2 flex items-center gap-2">
            <span class="text-[color:var(--color-brand)] font-semibold">{{ amountWithSymbol($product->price) }}</span>
            @if ($hasDiscount)
                <span class="text-xs text-neutral-400 line-through">{{ amountWithSymbol($product->compare_at_price) }}</span>
            @endif
        </div>
    </div>
</div>
