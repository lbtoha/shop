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

<div class="product-card group border border-[color:var(--color-line)] rounded-2xl p-4 hover:shadow-[0_8px_30px_rgba(0,0,0,0.06)] transition-shadow flex flex-col">
    <div class="relative">
        {{-- Badges --}}
        <div class="absolute top-0 left-0 z-10 flex flex-col gap-1.5">
            @if ($hasDiscount)
                <span class="bg-[color:var(--color-brand)] text-white text-[11px] font-semibold px-2 py-0.5 rounded-full">-{{ $discountPercent }}%</span>
            @endif
            @if ($isNew)
                <span class="bg-[color:var(--color-success-light)] text-[color:var(--color-ink)] text-[11px] font-semibold px-2 py-0.5 rounded-full">{{ __('NEW') }}</span>
            @endif
            @if (! $product->isInStock())
                <span class="bg-neutral-700 text-white text-[11px] font-semibold px-2 py-0.5 rounded-full">{{ __('SOLD OUT') }}</span>
            @endif
        </div>

        {{-- Image --}}
        <a href="{{ route('shop.product', $product->slug) }}" class="pc-image block rounded-xl bg-[color:var(--color-image)] mb-4 overflow-hidden aspect-square">
            @if ($primaryImage)
                <img src="{{ $primaryImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy">
            @else
                <div class="w-full h-full flex items-center justify-center text-neutral-300"><i class="ph ph-image text-5xl"></i></div>
            @endif
        </a>

        {{-- Centered slide-up action rail --}}
        <div class="pc-actions absolute right-0 left-0 flex justify-center z-10">
            <ul class="flex items-center shadow-md rounded-md overflow-hidden">
                <li>
                    <button type="button" aria-label="{{ __('Wishlist') }}"
                        class="size-11 bg-white inline-flex items-center justify-center text-[color:var(--color-body)] hover:bg-[color:var(--color-brand)] hover:text-white transition">
                        <i class="ph ph-heart text-xl"></i>
                    </button>
                </li>
                @if ($product->isInStock())
                    <li>
                        @if ($product->hasVariants())
                            {{-- Variant products need an option chosen — send to the detail page. --}}
                            <a aria-label="{{ __('Choose options') }}" href="{{ route('shop.product', $product->slug) }}"
                                class="size-11 bg-[color:var(--color-brand)] inline-flex items-center justify-center text-white hover:bg-[color:var(--color-brand-dark)] transition">
                                <i class="ph ph-sliders-horizontal text-xl"></i>
                            </a>
                        @else
                            <button type="button" aria-label="{{ __('Add to cart') }}" data-add-to-cart="{{ route('shop.cart.add', $product->id) }}"
                                class="size-11 bg-[color:var(--color-brand)] inline-flex items-center justify-center text-white hover:bg-[color:var(--color-brand-dark)] transition">
                                <i class="ph ph-shopping-cart-simple text-xl"></i>
                            </button>
                        @endif
                    </li>
                @endif
                <li>
                    <a aria-label="{{ __('Quick view') }}" href="{{ route('shop.product', $product->slug) }}"
                        class="size-11 bg-white inline-flex items-center justify-center text-[color:var(--color-body)] hover:bg-[color:var(--color-brand)] hover:text-white transition">
                        <i class="ph ph-eye text-xl"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Content --}}
    <div class="flex flex-col flex-1">
        @if ($product->category)
            <span class="text-[11px] uppercase tracking-wide text-[color:var(--color-muted)] mb-1">{{ $product->category->name }}</span>
        @endif
        <h5 class="text-sm font-semibold text-[color:var(--color-ink)] leading-5 line-clamp-2 min-h-[2.5rem]">
            <a href="{{ route('shop.product', $product->slug) }}" class="hover:text-[color:var(--color-brand)]">{{ $product->name }}</a>
        </h5>

        <div class="flex items-center gap-1 mt-2 text-xs">
            <span class="stars">
                @for ($i = 1; $i <= 5; $i++)<i class="ph-fill ph-star{{ $i <= round($rating) ? '' : ' text-neutral-300' }}"></i>@endfor
            </span>
            <span class="text-[color:var(--color-muted)]">({{ $reviewCount }})</span>
        </div>

        <div class="mt-2 flex items-center gap-2">
            <span class="text-[color:var(--color-ink)] font-bold">{{ amountWithSymbol($product->price) }}</span>
            @if ($hasDiscount)
                <span class="text-xs text-[color:var(--color-muted)] line-through">{{ amountWithSymbol($product->compare_at_price) }}</span>
            @endif
        </div>
    </div>
</div>
