@props(['product'])

@php
    $hasDiscount     = $product->compare_at_price && $product->compare_at_price > $product->price;
    $discountPercent = $hasDiscount
        ? round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100)
        : 0;
    $primaryImage    = $product->thumbnail
        ?: ($product->relationLoaded('images') ? optional($product->images->first())->image : null);
    $isNew           = $product->created_at && $product->created_at->gt(now()->subDays(14));
    $isFreeDelivery  = ((float)($product->shipping_cost_dhaka ?? 0)) == 0
                    && ((float)($product->shipping_cost_outside ?? 0)) == 0;
    $inStock         = $product->isInStock();

    // Gallery
    $gallery = collect();
    if ($product->thumbnail) $gallery->push($product->thumbnail);
    if ($product->relationLoaded('images')) {
        foreach ($product->images as $img) {
            if ($img->image) $gallery->push($img->image);
        }
    } else {
        $gallery = $gallery->concat($product->images->pluck('image')->filter());
    }
    $gallery = $gallery->unique()->values();

    $savedAmount = $hasDiscount ? ($product->compare_at_price - $product->price) : 0;

    // Second image for the hover swap (falls back to primary if only one).
    $hoverImage = $gallery->count() > 1 ? $gallery->get(1) : null;

    // Decorative placeholder rating (no review data yet) — deterministic per product.
    $ratingStars = 4 + ($product->id % 2);              // 4 or 5
    $reviewCount = 6 + ($product->id * 7) % 90;          // 6–95
@endphp

<div class="product-card group relative flex flex-col h-full bg-white rounded-2xl overflow-hidden border border-line hover:border-brand-mist transition-all duration-200 hover:-translate-y-1 hover:shadow-[var(--shadow-hover)]">

    {{-- ── Image area ────────────────────────────────── --}}
    <div class="relative overflow-hidden aspect-[3/4] bg-image shrink-0">
        <a href="{{ route('shop.product', $product->slug) }}" class="pc-image block w-full h-full">
            @if ($primaryImage)
                <img src="{{ $primaryImage }}" alt="{{ $product->name }}"
                     class="absolute inset-0 w-full h-full object-cover transition-all duration-500 {{ $hoverImage ? 'group-hover:opacity-0' : 'group-hover:scale-[1.04]' }}"
                     loading="lazy">
                @if ($hoverImage)
                    <img src="{{ $hoverImage }}" alt="{{ $product->name }}"
                         class="absolute inset-0 w-full h-full object-cover opacity-0 scale-105 transition-all duration-500 group-hover:opacity-100 group-hover:scale-100"
                         loading="lazy">
                @endif
            @else
                <div class="w-full h-full flex items-center justify-center text-subtle bg-image">
                    <i class="ph ph-image text-5xl"></i>
                </div>
            @endif
        </a>

        {{-- Badges (rounded pills) --}}
        <div class="absolute top-3 left-3 z-10 flex flex-wrap gap-1.5">
            @if ($hasDiscount)
                <span class="text-[11px] font-medium px-2.5 py-1 rounded-full bg-brand text-white tracking-wide">
                    -{{ $discountPercent }}%
                </span>
            @endif
            @if ($isNew)
                <span class="text-[11px] font-medium px-2.5 py-1 rounded-full bg-emerald-600 text-white tracking-wide">
                    {{ __('New') }}
                </span>
            @endif
        </div>

        {{-- Quick actions (reveal on hover) --}}
        @if ($inStock)
            <div class="absolute top-3 right-3 z-10 flex flex-col gap-2 translate-x-3 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                <a href="{{ route('shop.product', $product->slug) }}" aria-label="{{ __('Quick view') }}"
                   class="w-9 h-9 rounded-full bg-white/95 text-ink flex items-center justify-center shadow-md hover:bg-ink hover:text-white transition-colors">
                    <i class="ph ph-eye text-base"></i>
                </a>
                <button type="button" aria-label="{{ __('Wishlist') }}"
                   class="w-9 h-9 rounded-full bg-white/95 text-ink flex items-center justify-center shadow-md hover:bg-ink hover:text-white transition-colors">
                    <i class="ph ph-heart text-base"></i>
                </button>
                <a href="{{ route('shop.product', $product->slug) }}" aria-label="{{ __('Add to cart') }}"
                   class="w-9 h-9 rounded-full bg-white/95 text-ink flex items-center justify-center shadow-md hover:bg-ink hover:text-white transition-colors">
                    <i class="ph ph-shopping-bag text-base"></i>
                </a>
            </div>
        @endif

        {{-- Sold out overlay --}}
        @if (!$inStock)
            <div class="absolute inset-0 bg-white/55 backdrop-blur-[1px] z-10 flex items-center justify-center">
                <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-ink/85 text-white uppercase tracking-widest shadow">
                    {{ __('Sold Out') }}
                </span>
            </div>
        @endif
    </div>

    {{-- Delivery bar (full-width, under image) --}}
    @if ($isFreeDelivery && $inStock)
        <div class="bg-ink text-white text-xs font-medium flex items-center justify-center gap-1.5 py-2 tracking-widest">
            <i class="ph ph-truck text-base"></i>
            {{ __('Free delivery') }}
        </div>
    @endif

    {{-- ── Body ──────────────────────────────────────── --}}
    <div class="flex flex-col flex-1 p-4">

        {{-- Category --}}
        @if ($product->category)
            <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-muted mb-1.5">
                {{ $product->category->name }}
            </p>
        @endif

        {{-- Name --}}
        <a href="{{ route('shop.product', $product->slug) }}"
           class="text-[15px] font-medium text-ink leading-snug line-clamp-2 hover:text-brand transition-colors min-h-[2.6rem] mb-2">
            {{ $product->name }}
        </a>

        {{-- Rating (decorative) --}}
        <div class="flex items-center gap-1.5 mb-3">
            <div class="flex items-center gap-0.5">
                @for ($s = 1; $s <= 5; $s++)
                    <i class="ph-fill ph-star text-xs {{ $s <= $ratingStars ? 'text-gold' : 'text-line' }}"></i>
                @endfor
            </div>
            <span class="text-[11px] text-muted">({{ $reviewCount }})</span>
        </div>

        {{-- Pricing --}}
        <div class="flex items-baseline flex-wrap gap-2 mb-4">
            <span class="text-[22px] font-semibold text-ink">{{ amountWithSymbol($product->price) }}</span>
            @if ($hasDiscount)
                <span class="text-sm text-subtle line-through">{{ amountWithSymbol($product->compare_at_price) }}</span>
                <span class="text-[11px] font-semibold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">
                    {{ __('Save') }} {{ amountWithSymbol($savedAmount) }}
                </span>
            @endif
        </div>

        {{-- CTA --}}
        <div class="mt-auto">
            @if ($inStock)
                <a href="{{ route('shop.product', $product->slug) }}"
                   class="w-full py-3 rounded-xl bg-brand hover:bg-brand-dark active:scale-[0.98] text-white text-sm font-semibold flex items-center justify-center gap-2 transition-all duration-150 tracking-wide">
                    <i class="ph-fill ph-lightning text-base"></i>
                    {{ __('অর্ডার করুন') }}
                </a>
            @else
                <button disabled
                    class="w-full py-3 rounded-xl bg-line-soft text-subtle text-sm font-semibold flex items-center justify-center cursor-not-allowed tracking-wide">
                    {{ __('Sold Out') }}
                </button>
            @endif
        </div>
    </div>

</div>
