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
@endphp

<div class="product-card group relative flex flex-col h-full bg-white rounded-2xl overflow-hidden border border-neutral-100 shadow-sm hover:shadow-lg hover:shadow-neutral-200/80 hover:-translate-y-1 transition-all duration-250">

    {{-- ── Image area ────────────────────────────────── --}}
    <div class="relative overflow-hidden aspect-[3/4] bg-neutral-50 shrink-0">
        <a href="{{ route('shop.product', $product->slug) }}" class="pc-image block w-full h-full">
            @if ($primaryImage)
                <img src="{{ $primaryImage }}" alt="{{ $product->name }}"
                     class="w-full h-full object-cover"
                     loading="lazy"
                     data-original-src="{{ $primaryImage }}">
            @else
                <div class="w-full h-full flex items-center justify-center text-neutral-300 bg-neutral-100">
                    <i class="ph ph-image text-5xl"></i>
                </div>
            @endif
        </a>

        {{-- Discount ribbon --}}
        @if ($hasDiscount)
            <div class="absolute top-0 left-0 w-[72px] h-[72px] overflow-hidden z-10 pointer-events-none">
                <div class="absolute -rotate-45 bg-brand text-white text-[12px] font-black text-center py-1 w-[88px] -left-[26px] top-[8px] shadow-md select-none tracking-wider">
                    -{{ $discountPercent }}%
                </div>
            </div>
        @endif

        {{-- New badge --}}
        @if ($isNew && !$hasDiscount)
            <div class="absolute top-3 left-3 z-10">
                <span class="text-[10px] font-bold px-2 py-1 rounded-lg bg-accent text-white uppercase tracking-wider shadow-sm">
                    {{ __('New') }}
                </span>
            </div>
        @endif

        {{-- Sold out overlay --}}
        @if (!$inStock)
            <div class="absolute inset-0 bg-white/55 backdrop-blur-[1px] z-10 flex items-center justify-center">
                <span class="text-xs font-bold px-3 py-1.5 rounded-xl bg-neutral-900/85 text-white uppercase tracking-widest shadow">
                    {{ __('Sold Out') }}
                </span>
            </div>
        @endif

        {{-- Free delivery bar --}}
        @if ($isFreeDelivery && $inStock)
            <div class="absolute bottom-0 inset-x-0 z-10 bg-success/90 text-white text-[10px] font-bold tracking-widest flex items-center justify-center gap-1.5 py-1.5">
                <i class="ph ph-truck text-sm"></i>
                {{ __('Free Delivery') }}
            </div>
        @endif
    </div>

    {{-- ── Body ──────────────────────────────────────── --}}
    <div class="flex flex-col flex-1 p-3.5 sm:p-4 gap-2">

        {{-- Gallery thumbnails --}}
        @if ($gallery->count() > 1)
            <div class="flex items-center gap-1 overflow-x-auto scrollbar-none pb-0.5 -mx-0.5 px-0.5">
                @foreach ($gallery->take(4) as $idx => $img)
                    <button type="button"
                        class="w-7 h-7 rounded-md border-2 {{ $idx === 0 ? 'border-brand' : 'border-neutral-200' }} overflow-hidden shrink-0 transition-all duration-150 hover:border-brand bg-white p-0.5"
                        onmouseover="
                            const card = this.closest('.product-card');
                            const mainImg = card.querySelector('.pc-image img');
                            if (mainImg) mainImg.src = '{{ $img }}';
                            card.querySelectorAll('button[class*=border]').forEach(b => b.classList.replace('border-brand','border-neutral-200'));
                            this.classList.replace('border-neutral-200','border-brand');
                        "
                        onmouseleave="
                            const card = this.closest('.product-card');
                            const mainImg = card.querySelector('.pc-image img');
                            if (mainImg) mainImg.src = mainImg.getAttribute('data-original-src');
                            card.querySelectorAll('button[class*=border]').forEach((b, i) => {
                                b.classList.replace('border-brand','border-neutral-200');
                                if (i === 0) b.classList.replace('border-neutral-200','border-brand');
                            });
                        ">
                        <img src="{{ $img }}" class="w-full h-full object-cover rounded-[3px]" loading="lazy">
                    </button>
                @endforeach
                @if ($gallery->count() > 4)
                    <span class="text-[10px] font-bold text-muted ml-1 shrink-0">+{{ $gallery->count() - 4 }}</span>
                @endif
            </div>
        @endif

        {{-- Category --}}
        @if ($product->category)
            <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-muted leading-none">
                {{ $product->category->name }}
            </p>
        @endif

        {{-- Name --}}
        <a href="{{ route('shop.product', $product->slug) }}"
           class="text-[13px] sm:text-sm font-semibold text-ink leading-snug line-clamp-2 hover:text-brand transition-colors min-h-[2.6rem]">
            {{ $product->name }}
        </a>

        {{-- Price --}}
        <div class="flex items-baseline flex-wrap gap-1.5 mt-auto">
            <span class="text-lg sm:text-xl font-extrabold text-ink">{{ amountWithSymbol($product->price) }}</span>
            @if ($hasDiscount)
                <span class="text-xs text-neutral-400 line-through">{{ amountWithSymbol($product->compare_at_price) }}</span>
                <span class="text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60 px-1.5 py-0.5 rounded-md">
                    {{ __('Save') }} {{ amountWithSymbol($savedAmount) }}
                </span>
            @endif
        </div>

        {{-- CTA --}}
        <div class="pt-1">
            @if ($inStock)
                <a href="{{ route('shop.product', $product->slug) }}"
                   class="w-full flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark active:scale-[0.97] text-white text-xs sm:text-sm font-bold py-2.5 rounded-xl transition-all duration-150 shadow-sm shadow-brand/20 tracking-wide">
                    <i class="ph ph-lightning text-sm"></i>
                    {{ __('অর্ডার করুন') }}
                </a>
            @else
                <button disabled
                    class="w-full flex items-center justify-center text-xs sm:text-sm font-semibold py-2.5 rounded-xl bg-neutral-100 text-neutral-400 cursor-not-allowed tracking-wide">
                    {{ __('Sold Out') }}
                </button>
            @endif
        </div>
    </div>

</div>
