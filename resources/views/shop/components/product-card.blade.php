@props(['product'])

@php
    $hasDiscount = $product->compare_at_price && $product->compare_at_price > $product->price;
    $discountPercent = $hasDiscount
        ? round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100)
        : 0;
    $primaryImage = $product->thumbnail ?: ($product->relationLoaded('images') ? optional($product->images->first())->image : null);
    $isNew = $product->created_at && $product->created_at->gt(now()->subDays(14));
    
    // Free delivery if both inside and outside Dhaka shipping costs are 0
    $isFreeDelivery = ((float) ($product->shipping_cost_dhaka ?? 0)) == 0 && ((float) ($product->shipping_cost_outside ?? 0)) == 0;

    // Collect gallery images (main thumbnail + product images)
    $gallery = collect();
    if ($product->thumbnail) {
        $gallery->push($product->thumbnail);
    }
    if ($product->relationLoaded('images')) {
        foreach ($product->images as $img) {
            if ($img->image) {
                $gallery->push($img->image);
            }
        }
    } else {
        $gallery = $gallery->concat($product->images->pluck('image')->filter());
    }
    $gallery = $gallery->unique()->values();
@endphp

<!-- Card -->
<div class="product-card w-full bg-white rounded-2xl overflow-hidden border border-neutral-100 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200 cursor-pointer group flex flex-col justify-between h-full">

    <div>
        <!-- Image Container -->
        <div class="relative overflow-hidden aspect-[3/4] w-full bg-neutral-50 flex items-center justify-center">
            <a href="{{ route('shop.product', $product->slug) }}" class="pc-image block w-full h-full">
                @if ($primaryImage)
                    <img src="{{ $primaryImage }}" alt="{{ $product->name }}" 
                        class="w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-500" 
                        loading="lazy"
                        data-original-src="{{ $primaryImage }}">
                @else
                    <div class="w-full h-full flex items-center justify-center text-neutral-300 bg-neutral-100">
                        <i class="ph ph-image text-5xl"></i>
                    </div>
                @endif
            </a>

            <!-- Badges -->
            @if ($hasDiscount)
                <div class="absolute top-0 left-0 w-20 h-20 overflow-hidden z-10 pointer-events-none">
                    <div class="absolute -rotate-45 bg-[#ff4e00] text-white text-[14px] font-black text-center py-1 w-[90px] -left-[28px] top-[8px] shadow-sm select-none uppercase tracking-wider">
                        -{{ $discountPercent }}%
                    </div>
                </div>
            @endif

            @if (! $product->isInStock())
                <div class="absolute top-3 right-3 z-10">
                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-neutral-900/90 text-white tracking-wide shadow-sm uppercase">
                        {{ __('SOLD OUT') }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Delivery Bar -->
        @if ($isFreeDelivery)
            <div class="bg-neutral-900 text-white text-xs font-medium flex items-center justify-center gap-1.5 py-2 tracking-widest">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a1 1 0 0 1 1 1v3"/>
                    <rect x="9" y="11" width="14" height="10" rx="2"/>
                    <circle cx="12" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                </svg>
                {{ __('Free delivery') }}
            </div>
        @endif
    </div>

    <!-- Body -->
    <div class="p-4 flex flex-col justify-between flex-1">
        <div>
            {{-- Gallery Thumbnails --}}
            @if ($gallery->count() > 1)
                <div class="flex items-center gap-1 mb-3 px-0.5 justify-start overflow-x-auto scrollbar-none py-0.5">
                    @foreach ($gallery->take(4) as $idx => $img)
                        <button type="button"
                            class="w-7 h-7 rounded-md border {{ $idx === 0 ? 'border-brand ring-1 ring-brand/10' : 'border-neutral-200' }} overflow-hidden cursor-pointer hover:border-brand transition p-0.5 bg-white shrink-0"
                            onmouseover="const img = this.closest('.product-card').querySelector('.pc-image img'); if(img) { img.src='{{ $img }}'; } this.parentElement.querySelectorAll('button').forEach(b => b.classList.replace('border-brand', 'border-neutral-200')); this.classList.replace('border-neutral-200', 'border-brand');"
                            onmouseleave="const img = this.closest('.product-card').querySelector('.pc-image img'); if(img) { img.src=img.getAttribute('data-original-src'); } this.parentElement.querySelectorAll('button').forEach((b, i) => { b.classList.replace('border-brand', 'border-neutral-200'); if (i===0) b.classList.replace('border-neutral-200', 'border-brand'); });">
                            <img src="{{ $img }}" class="w-full h-full object-cover rounded">
                        </button>
                    @endforeach
                    @if ($gallery->count() > 4)
                        <span class="text-[10px] font-black text-neutral-400 ml-1.5 shrink-0">+{{ $gallery->count() - 4 }}</span>
                    @endif
                </div>
            @endif

            <!-- Category -->
            @if ($product->category)
                <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-neutral-400 mb-1.5">
                    {{ $product->category->name }}
                </p>
            @endif

            <!-- Product Name -->
            <p class="text-[15px] font-medium text-neutral-900 leading-snug mb-3.5 min-h-[2.5rem] line-clamp-2 hover:text-brand transition-colors">
                <a href="{{ route('shop.product', $product->slug) }}">{{ $product->name }}</a>
            </p>

            <!-- Pricing -->
            <div class="flex items-baseline gap-2 mb-4 flex-wrap">
                <span class="text-[22px] font-semibold text-neutral-900">{{ amountWithSymbol($product->price) }}</span>
                @if ($hasDiscount)
                    <span class="text-sm text-neutral-400 line-through">{{ amountWithSymbol($product->compare_at_price) }}</span>
                    @php
                        $savedAmount = $product->compare_at_price - $product->price;
                    @endphp
                    <span class="text-[11px] font-semibold bg-amber-100 text-amber-800 px-2.5 py-0.5 rounded-full">
                        {{ __('Save') }} {{ amountWithSymbol($savedAmount) }}
                    </span>
                @endif
            </div>
        </div>

        <!-- CTA Button -->
        <div>
            @if ($product->isInStock())
                <a href="{{ route('shop.product', $product->slug) }}"
                    class="w-full py-3 rounded-xl bg-brand hover:bg-brand-dark active:scale-[0.98] text-white text-sm font-semibold flex items-center justify-center gap-2 transition-all duration-150 tracking-wide shadow-sm">
                    <!-- Lightning bolt icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M13 2L4.09 12.78A1 1 0 0 0 5 14h6v8l8.91-10.78A1 1 0 0 0 19 10h-6V2z"/>
                    </svg>
                    {{ __('অর্ডার করুন') }}
                </a>
            @else
                <button type="button" disabled
                    class="w-full py-3 rounded-xl bg-neutral-100 text-neutral-400 text-sm font-semibold flex items-center justify-center gap-2 cursor-not-allowed">
                    <span>{{ __('SOLD OUT') }}</span>
                </button>
            @endif
        </div>
    </div>

</div>
