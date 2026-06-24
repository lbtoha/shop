@extends('shop.layouts.app')

@section('title', $product->name . ' — ' . config('application_info.company_info.name'))

@section('content')
    @php
        $hasDiscount = $product->compare_at_price && $product->compare_at_price > $product->price;
        $gallery = $product->images->pluck('image')->filter()->values();
        if ($product->thumbnail) {
            $gallery->prepend($product->thumbnail);
        }
        $gallery = $gallery->unique()->values();
        $mainImage = $gallery->first();

        // Unified media list for the gallery: images first, then an optional video.
        $videoEmbed = $product->videoEmbedUrl();
        $media = $gallery->map(fn ($img) => ['type' => 'image', 'src' => $img])->values();
        if ($videoEmbed) {
            $media->push([
                'type' => 'video',
                'embed' => $videoEmbed,
                'poster' => $mainImage, // play-icon thumbnail uses the main image as backdrop
            ]);
        }
        $phone = getOption('whatsapp_number') ?: (config('application_info.company_info.phone') ?: '01935100013');
        $whatsappEnabled = (int) getOption('whatsapp_enabled', 0) === 1 && filled(getOption('whatsapp_number'));
        $whatsappNumber = getOption('whatsapp_number', '');
        $whatsappLink = 'https://wa.me/'.preg_replace('/[^0-9]/', '', $whatsappNumber).'?text='.rawurlencode(__('Hi, I am interested in :product', ['product' => $product->name]));
        $showCategory = (int) getOption('show_product_category', 1) === 1;
        $tryOnEnabled = \App\Services\Ai\GeminiTryOnService::isEnabled();
        $tryOnCaptcha = $tryOnEnabled && \App\Services\Ai\TryOnAbuseGuard::captchaEnabled();
        $shareUrl = urlencode(request()->fullUrl());
        $isFreeDelivery = ((float) ($product->shipping_cost_dhaka ?? 0)) == 0 && ((float) ($product->shipping_cost_outside ?? 0)) == 0;

        // Variant option groups (e.g. Color => [Red, Blue], Size => [S, M, L])
        // and a JS-friendly map of "Color|Size" combos to id/price/stock.
        $hasVariants = $product->variants->isNotEmpty();
        $optionGroups = [];
        $variantMap = [];
        if ($hasVariants) {
            foreach ($product->variants as $v) {
                $attrs = $v->attributes ?? [];
                foreach ($attrs as $key => $val) {
                    $optionGroups[$key] = $optionGroups[$key] ?? [];
                    if (! in_array($val, $optionGroups[$key], true)) {
                        $optionGroups[$key][] = $val;
                    }
                }
            }
            foreach ($product->variants as $v) {
                $attrs = $v->attributes ?? [];
                $comboParts = [];
                foreach (array_keys($optionGroups) as $gk) {
                    $comboParts[] = $attrs[$gk] ?? '';
                }
                $comboKey = implode('|', $comboParts);
                $variantMap[$comboKey] = [
                    'id' => $v->id,
                    'price' => $v->price(),
                    'price_label' => amountWithSymbol($v->price()),
                    'stock' => (int) $v->stock,
                    'name' => $v->name,
                ];
            }
        }
    @endphp

    <div class="shop-container py-8">
        {{-- Breadcrumb --}}
        <nav class="text-xs text-neutral-400 mb-6 font-bold tracking-wider uppercase">
            <a href="{{ route('home') }}" class="hover:text-brand transition">{{ __('Home') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('shop.index') }}" class="hover:text-brand transition">{{ __('Shop') }}</a>
            <span class="mx-2">/</span>
            <span class="text-neutral-800">{{ $product->name }}</span>
        </nav>

        {{-- Product Details Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            {{-- Image Gallery Column --}}
            <div class="lg:col-span-5 flex flex-col sm:flex-row gap-4 lg:sticky lg:top-24 relative">
                {{-- Thumbnails (images + optional video) --}}
                @if ($media->count() > 1)
                    <div class="flex sm:flex-col gap-3 order-2 sm:order-1 overflow-x-auto sm:overflow-visible shrink-0 py-1">
                        @foreach ($media as $index => $m)
                            <button type="button"
                                data-media-thumb="{{ $index }}"
                                data-media-type="{{ $m['type'] }}"
                                @if ($m['type'] === 'image') data-media-src="{{ $m['src'] }}"
                                @else data-media-embed="{{ $m['embed'] }}" @endif
                                class="relative w-14 h-18 rounded-md border-2 {{ $index === 0 ? 'border-brand ring-2 ring-brand/10' : 'border-neutral-100' }} overflow-hidden hover:border-brand transition focus:outline-none p-0.5 shrink-0">
                                <img src="{{ $m['type'] === 'image' ? $m['src'] : $m['poster'] }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-md">
                                @if ($m['type'] === 'video')
                                    <span class="absolute inset-0 flex items-center justify-center bg-black/30 rounded-md">
                                        <span class="w-6 h-6 rounded-full bg-white/90 flex items-center justify-center shadow">
                                            <i class="ph-fill ph-play text-brand text-xs ml-0.5"></i>
                                        </span>
                                    </span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                @endif
                {{-- Primary Viewer --}}
                <div id="main-image-container" class="flex-1 rounded-md overflow-hidden bg-neutral-50 aspect-[4/5] max-h-[500px] shadow-sm order-1 sm:order-2 relative">
                    @if ($mainImage)
                        <img id="main-product-image" src="{{ $mainImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover">

                        {{-- Video iframe holder (hidden until a video thumb is chosen) --}}
                        <div id="main-video-wrap" class="absolute inset-0 z-20 hidden bg-black">
                            <iframe id="main-video-frame" src="" class="w-full h-full" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen></iframe>
                        </div>

                        {{-- Zoom Lens (circular, matches reference site) --}}
                        <div id="zoom-lens" class="absolute pointer-events-none hidden z-10 rounded-full shadow-2xl border-2" style="width:140px;height:140px;border-color:rgba(225,29,72,0.35);background:rgba(225,29,72,0.06);backdrop-filter:blur(2px);"></div>

                        {{-- Prev / Next arrows --}}
                        @if ($media->count() > 1)
                            <button type="button" id="gallery-prev"
                                class="absolute left-0 top-1/2 -translate-y-1/2 z-30 w-8 h-12 rounded-r-full bg-brand-soft text-brand hover:bg-brand hover:text-white flex items-center justify-start pl-2 border border-l-0 border-brand-mist hover:border-brand transition-all duration-300 shadow-sm hover:shadow-md cursor-pointer focus:outline-none">
                                <i class="ph-bold ph-caret-left text-base"></i>
                            </button>
                            <button type="button" id="gallery-next"
                                class="absolute right-0 top-1/2 -translate-y-1/2 z-30 w-8 h-12 rounded-l-full bg-brand-soft text-brand hover:bg-brand hover:text-white flex items-center justify-end pr-2 border border-r-0 border-brand-mist hover:border-brand transition-all duration-300 shadow-sm hover:shadow-md cursor-pointer focus:outline-none">
                                <i class="ph-bold ph-caret-right text-base"></i>
                            </button>
                        @endif
                    @else
                        <div class="w-full h-full flex items-center justify-center text-neutral-300">
                            <i class="ph ph-image text-7xl"></i>
                        </div>
                    @endif
                </div>

                {{-- Zoom Result Window (Desktop only, background-image approach — no child img) --}}
                @if ($mainImage)
                    <div id="zoom-result" class="absolute left-[calc(100%+1.5rem)] top-0 w-full h-full rounded-md overflow-hidden shadow-2xl border border-neutral-100 bg-neutral-50 z-30 hidden pointer-events-none bg-no-repeat"></div>
                @endif
            </div>

            {{-- Product Info & Action Column --}}
            <div class="lg:col-span-7 flex flex-col justify-between self-stretch">
                <div>
                    @if ($showCategory && $product->category)
                        <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}"
                            class="inline-block text-[10px] font-black tracking-widest text-brand uppercase bg-brand/5 hover:bg-brand/10 px-2.5 py-1 rounded-full mb-2 transition">{{ $product->category->name }}</a>
                    @endif
                    
                    <h1 class="text-2xl sm:text-3xl font-black text-neutral-900 tracking-tight leading-tight">{{ $product->name }}</h1>

                    {{-- Price Row --}}
                    <div class="mt-2.5 flex items-center gap-3 flex-wrap">
                        <span id="product-price" class="text-3xl font-black text-brand tracking-tight">{{ amountWithSymbol($product->displayPrice()) }}</span>
                        @if ($hasDiscount)
                            <span class="text-lg text-neutral-400 line-through font-semibold">{{ amountWithSymbol($product->compare_at_price) }}</span>
                            @php
                                $percent = round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100);
                            @endphp
                            <span class="bg-brand-soft text-brand border border-brand-mist text-xs font-extrabold uppercase tracking-widest px-2.5 py-0.5 rounded-md">{{ __('SAVE :percent%', ['percent' => $percent]) }}</span>
                        @endif

                        @if ($isFreeDelivery)
                            <span class="bg-accent/10 text-accent-dark border border-accent/20 text-xs font-extrabold uppercase tracking-widest px-2.5 py-0.5 rounded-md flex items-center gap-1">
                                <i class="ph ph-truck text-sm"></i>
                                <span>{{ __('Free Delivery') }}</span>
                            </span>
                        @endif

                        {{-- Availability aligned to right --}}
                        <div class="ml-auto" id="variant-status">
                            @if ($hasVariants)
                                <span class="inline-flex items-center gap-1 text-sm font-bold text-neutral-400 uppercase tracking-wider">{{ __('Select options') }}</span>
                            @elseif ($product->isInStock())
                                <span class="inline-flex items-center gap-1.5 text-sm font-bold text-emerald-600 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    {{ __('In Stock') }} ({{ $product->effectiveStock() }})
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-sm font-bold text-red-600 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    {{ __('Out of Stock') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Variant Option Pickers --}}
                    @if ($hasVariants)
                        <div id="variant-picker" class="mt-4 pt-3 border-t border-neutral-100 space-y-3"
                            data-variant-map='@json($variantMap)' data-option-groups='@json($optionGroups)'>
                            @foreach ($optionGroups as $groupName => $values)
                                <div class="flex items-center gap-4">
                                    <div class="text-xs font-black text-neutral-500 uppercase tracking-widest w-20 shrink-0">
                                        {{ __($groupName) }}
                                    </div>
                                    <div class="flex items-center gap-2 flex-wrap" data-option-group="{{ $groupName }}">
                                        @foreach ($values as $val)
                                            <button type="button" data-option-value="{{ $val }}"
                                                class="h-10 min-w-[40px] px-3 rounded-md border border-neutral-200 text-sm font-bold text-neutral-800 flex items-center justify-center bg-white hover:border-neutral-800 transition-all duration-200 focus:outline-none">
                                                {{ $val }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Add to Cart / Order Now actions --}}
                    @php
                        $inWishlist = app(\App\Services\Ecommerce\Wishlist::class)->has($product->id);
                    @endphp
                    @if ($product->isInStock())
                        <div class="mt-5 pt-4 border-t border-neutral-100 flex flex-col sm:flex-row items-stretch gap-3">
                            <input type="hidden" id="selected-variant-id" name="variant_id" value="">

                            {{-- Quantity Stepper --}}
                            <div data-qty-wrap class="flex items-center justify-between border border-neutral-200 rounded-md px-1.5 shrink-0 bg-neutral-50/50">
                                <button type="button" data-step="-1" class="w-8 h-10 text-lg font-bold text-neutral-500 hover:text-brand transition">−</button>
                                <input type="number" data-quantity-input value="1" min="1" max="{{ $hasVariants ? 99 : $product->effectiveStock() }}"
                                    class="w-8 text-center py-2 text-sm font-black text-neutral-800 focus:outline-none bg-transparent select-none">
                                <button type="button" data-step="1" class="w-8 h-10 text-lg font-bold text-neutral-500 hover:text-brand transition">+</button>
                            </div>

                            {{-- Add to Cart --}}
                            <button type="button" id="btn-add-to-cart" data-add-to-cart="{{ route('shop.cart.add', $product->id) }}"
                                @if ($hasVariants) disabled @endif
                                class="flex-1 bg-brand hover:bg-brand-dark hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] text-white font-black py-2.5 px-5 rounded-md transition-all duration-200 text-xs tracking-wider uppercase flex items-center justify-center gap-2 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                <i class="ph ph-shopping-cart-simple text-base"></i>
                                <span>{{ __('ADD TO CART') }}</span>
                            </button>

                            {{-- Order Now --}}
                            <button type="button" id="btn-order-now" data-buy-now="{{ route('shop.cart.add', $product->id) }}"
                                @if ($hasVariants) disabled @endif
                                class="flex-1 bg-neutral-900 hover:bg-black hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] text-white font-black py-2.5 px-5 rounded-md transition-all duration-200 text-xs tracking-wider uppercase flex items-center justify-center gap-2 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                <i class="ph ph-lightning text-base"></i>
                                <span>{{ __('ORDER NOW') }}</span>
                            </button>

                            {{-- Wishlist --}}
                            <button type="button"
                                    data-wishlist-toggle="{{ route('shop.wishlist.toggle', $product->id) }}"
                                    data-product-id="{{ $product->id }}"
                                    class="w-12 h-12 rounded-md flex items-center justify-center border transition-all duration-200 shrink-0 {{ $inWishlist ? 'bg-brand text-white border-brand' : 'bg-white text-neutral-700 border-neutral-200 hover:bg-neutral-50' }}">
                                <i class="ph ph-heart text-xl {{ $inWishlist ? 'ph-fill' : '' }}"></i>
                            </button>
                        </div>
                    @else
                        <div class="mt-5 pt-4 border-t border-neutral-100 flex items-center gap-3">
                            <button disabled class="flex-1 bg-neutral-100 text-neutral-400 font-bold py-2.5 px-5 rounded-md text-xs tracking-wider uppercase cursor-not-allowed">
                                {{ __('OUT OF STOCK') }}
                            </button>

                            {{-- Wishlist --}}
                            <button type="button"
                                    data-wishlist-toggle="{{ route('shop.wishlist.toggle', $product->id) }}"
                                    data-product-id="{{ $product->id }}"
                                    class="w-12 h-12 rounded-md flex items-center justify-center border transition-all duration-200 shrink-0 {{ $inWishlist ? 'bg-brand text-white border-brand' : 'bg-white text-neutral-700 border-neutral-200 hover:bg-neutral-50' }}">
                                <i class="ph ph-heart text-xl {{ $inWishlist ? 'ph-fill' : '' }}"></i>
                            </button>
                        </div>
                    @endif

                    {{-- AI Virtual Try-On --}}
                    @if ($tryOnEnabled)
                        <button type="button" id="btn-try-on"
                            class="mt-3 w-full flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-700 hover:to-fuchsia-700 text-white font-black py-2.5 px-5 rounded-md transition-all duration-200 text-xs tracking-wider uppercase shadow-sm">
                            <i class="ph ph-sparkle text-base"></i>
                            <span>{{ __('Try it On with AI') }}</span>
                        </button>
                    @endif

                    {{-- Trust Badges --}}
                    <div class="mt-4 bg-neutral-50/50 rounded-md p-2.5 grid grid-cols-3 gap-2 text-center text-neutral-500">
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-1.5">
                            <i class="ph ph-truck text-lg text-neutral-700"></i>
                            <span class="text-[10px] sm:text-[11px] font-extrabold tracking-wider uppercase text-neutral-600 leading-tight">{{ __('Fast Delivery') }}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-1.5 border-x border-neutral-200">
                            <i class="ph ph-shield-check text-lg text-neutral-700"></i>
                            <span class="text-[10px] sm:text-[11px] font-extrabold tracking-wider uppercase text-neutral-600 leading-tight">{{ __('Secure Checkout') }}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-1.5">
                            <i class="ph ph-arrow-counter-clockwise text-lg text-neutral-700"></i>
                            <span class="text-[10px] sm:text-[11px] font-extrabold tracking-wider uppercase text-neutral-600 leading-tight">{{ __('Easy Returns') }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    {{-- Support & WhatsApp Bar --}}
                    <div class="mt-4 bg-neutral-50 rounded-md p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-sm">
                        <span class="text-neutral-500 font-extrabold uppercase tracking-wider text-xs">{{ __('Need Help?') }}</span>
                        <div class="flex flex-col xs:flex-row items-start xs:items-center gap-2.5 xs:gap-4 flex-wrap">
                            <a href="tel:{{ $phone }}" class="font-extrabold text-brand hover:underline flex items-center gap-1">
                                <i class="ph ph-phone text-base"></i> {{ $phone }}
                            </a>
                            @if ($whatsappEnabled)
                                <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="font-extrabold text-emerald-600 hover:underline flex items-center gap-1">
                                    <i class="ph ph-whatsapp-logo text-base"></i> {{ $whatsappNumber }}
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Product Metadata and Share Row --}}
                    <div class="mt-3 flex items-center justify-between text-xs border-t border-neutral-100 pt-3 flex-wrap gap-2">
                        <div class="flex items-center gap-4 text-neutral-400 font-bold">
                            @if ($product->sku)
                                <div>SKU: <span class="text-neutral-800 font-extrabold uppercase">{{ $product->sku }}</span></div>
                            @endif
                            @if ($showCategory && $product->category)
                                <div>CATEGORY: <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="text-brand hover:underline uppercase font-extrabold">{{ $product->category->name }}</a></div>
                            @endif
                        </div>
                        
                        {{-- Share inline --}}
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-neutral-400 uppercase tracking-widest mr-1">{{ __('SHARE') }}</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener"
                                class="w-8 h-8 rounded-full bg-neutral-100 hover:bg-[#1877f2] hover:text-white text-neutral-600 flex items-center justify-center transition"><i class="ph ph-facebook-logo text-base"></i></a>
                            <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}" target="_blank" rel="noopener"
                                class="w-8 h-8 rounded-full bg-neutral-100 hover:bg-[#1da1f2] hover:text-white text-neutral-600 flex items-center justify-center transition"><i class="ph ph-twitter-logo text-base"></i></a>
                            <a href="https://wa.me/?text={{ $shareUrl }}" target="_blank" rel="noopener"
                                class="w-8 h-8 rounded-full bg-neutral-100 hover:bg-emerald-500 hover:text-white text-neutral-600 flex items-center justify-center transition"><i class="ph ph-whatsapp-logo text-base"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Description Section --}}
        @if ($product->description)
            <section class="mt-12 border-t border-neutral-200 pt-8 max-w-4xl">
                <div class="border-b border-neutral-200 pb-2 mb-6">
                    <h2 class="text-xs sm:text-sm font-bold text-brand uppercase tracking-widest relative inline-block pb-2">
                        {{ __('PRODUCT DESCRIPTION') }}
                        <span class="absolute bottom-0 left-0 w-full h-[2px] bg-brand"></span>
                    </h2>
                </div>
                <div class="prose prose-neutral max-w-none text-neutral-600 leading-relaxed text-sm sm:text-base">
                    {!! $product->description !!}
                </div>
            </section>
        @endif

        {{-- Related Products Section (same category) --}}
        @if ($showCategory && $related->isNotEmpty())
            <section class="mt-12 border-t border-neutral-200 pt-8">
                <div class="border-b border-neutral-200 pb-2 mb-6 flex justify-between items-end">
                    <h2 class="text-xs sm:text-sm font-bold text-brand uppercase tracking-widest relative inline-block pb-2">
                        {{ __('RELATED PRODUCTS') }}
                        <span class="absolute bottom-0 left-0 w-full h-[2px] bg-brand"></span>
                    </h2>
                    <a href="{{ route('shop.index', ['category' => $product->category?->slug]) }}"
                        class="text-[10px] font-bold text-neutral-400 hover:text-brand transition uppercase tracking-wider mb-2">
                        {{ __('SEE ALL') }}
                    </a>
                </div>
                <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                    @foreach ($related as $item)
                        <x-shop::product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- You May Also Like Section --}}
        @if (isset($recommended) && $recommended->isNotEmpty())
            <section class="mt-12 border-t border-neutral-200 pt-8">
                <div class="border-b border-neutral-200 pb-2 mb-6">
                    <h2 class="text-xs sm:text-sm font-bold text-brand uppercase tracking-widest relative inline-block pb-2">
                        {{ __('YOU MIGHT ALSO LIKE') }}
                        <span class="absolute bottom-0 left-0 w-full h-[2px] bg-brand"></span>
                    </h2>
                </div>
                <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                    @foreach ($recommended as $item)
                        <x-shop::product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    @push('scripts')
        <script>
            // ─── Media gallery: images + optional video, thumbs + prev/next ───
            (function () {
                var thumbs = Array.prototype.slice.call(document.querySelectorAll('[data-media-thumb]'));
                var mainImg = document.getElementById('main-product-image');
                var zoomResImg = document.getElementById('zoom-result-image');
                var videoWrap = document.getElementById('main-video-wrap');
                var videoFrame = document.getElementById('main-video-frame');
                var container = document.getElementById('main-image-container');
                var prevBtn = document.getElementById('gallery-prev');
                var nextBtn = document.getElementById('gallery-next');
                if (!mainImg) return;

                // Build the media list from the thumbnails (works with 0/1 thumbs too).
                var media = thumbs.length
                    ? thumbs.map(function (b) {
                        return {
                            type: b.getAttribute('data-media-type'),
                            src: b.getAttribute('data-media-src'),
                            embed: b.getAttribute('data-media-embed'),
                        };
                    })
                    : [{ type: 'image', src: mainImg.getAttribute('src') }];

                var current = 0;

                function highlight(index) {
                    thumbs.forEach(function (t) {
                        t.classList.remove('border-brand', 'ring-2', 'ring-brand/10');
                        t.classList.add('border-neutral-100');
                    });
                    var active = thumbs[index];
                    if (active) {
                        active.classList.remove('border-neutral-100');
                        active.classList.add('border-brand', 'ring-2', 'ring-brand/10');
                    }
                }

                function showVideo(on) {
                    if (!videoWrap) return;
                    videoWrap.classList.toggle('hidden', !on);
                    if (container) container.classList.toggle('cursor-zoom-in', !on);
                }

                function select(index) {
                    if (index < 0) index = media.length - 1;
                    if (index >= media.length) index = 0;
                    current = index;
                    var item = media[index];

                    if (item.type === 'video') {
                        if (videoFrame && videoFrame.getAttribute('src') !== item.embed) {
                            videoFrame.setAttribute('src', item.embed);
                        }
                        showVideo(true);
                    } else {
                        showVideo(false);
                        if (videoFrame) videoFrame.setAttribute('src', ''); // stop playback
                        if (mainImg && item.src) mainImg.src = item.src;
                        if (zoomResImg && item.src) zoomResImg.src = item.src;
                    }
                    highlight(index);
                }

                thumbs.forEach(function (b, i) {
                    b.addEventListener('click', function () { select(i); });
                });
                if (prevBtn) prevBtn.addEventListener('click', function () { select(current - 1); });
                if (nextBtn) nextBtn.addEventListener('click', function () { select(current + 1); });

                // Expose current-media check for the zoom feature.
                window.__galleryIsVideo = function () { return media[current] && media[current].type === 'video'; };
            })();

            // Premium Side-by-Side Hover Zoom (Desktop only)
            // Uses background-image on the result panel — main image is NEVER touched.
            (function () {
                var container = document.getElementById('main-image-container');
                var mainImg  = document.getElementById('main-product-image');
                var lens     = document.getElementById('zoom-lens');
                var result   = document.getElementById('zoom-result');

                if (!container || !mainImg || !lens || !result) return;

                var SCALE = 3; // zoom magnification

                container.addEventListener('mousemove', function (e) {
                    if (window.innerWidth < 1024) return;
                    if (window.__galleryIsVideo && window.__galleryIsVideo()) return;

                    // Show on first move (avoids flicker on plain mouseenter)
                    lens.classList.remove('hidden');
                    result.classList.remove('hidden');
                    container.style.cursor = 'crosshair';

                    var rect = mainImg.getBoundingClientRect();
                    var x = e.clientX - rect.left;
                    var y = e.clientY - rect.top;

                    // Clamp lens so it stays inside the image
                    var lensX = x - lens.offsetWidth  / 2;
                    var lensY = y - lens.offsetHeight / 2;
                    if (lensX < 0) lensX = 0;
                    if (lensY < 0) lensY = 0;
                    if (lensX > rect.width  - lens.offsetWidth)  lensX = rect.width  - lens.offsetWidth;
                    if (lensY > rect.height - lens.offsetHeight) lensY = rect.height - lens.offsetHeight;

                    lens.style.left = lensX + 'px';
                    lens.style.top  = lensY + 'px';

                    // Drive the result via background-image — the main image is never modified
                    result.style.backgroundImage    = 'url(' + mainImg.src + ')';
                    result.style.backgroundSize     = (rect.width * SCALE) + 'px ' + (rect.height * SCALE) + 'px';
                    result.style.backgroundPosition = '-' + (lensX * SCALE) + 'px -' + (lensY * SCALE) + 'px';
                });

                container.addEventListener('mouseleave', function () {
                    lens.classList.add('hidden');
                    result.classList.add('hidden');
                    container.style.cursor = '';
                    // Nothing to reset on mainImg — it was never touched.
                });
            })();

            // Variant picker: selecting one value per option group resolves a variant.
            (function () {
                var picker = document.getElementById('variant-picker');
                if (!picker) return;

                var variantMap = JSON.parse(picker.getAttribute('data-variant-map') || '{}');
                var optionGroups = JSON.parse(picker.getAttribute('data-option-groups') || '{}');
                var groupNames = Object.keys(optionGroups);
                var selected = {};

                var priceEl = document.getElementById('product-price');
                var statusEl = document.getElementById('variant-status');
                var variantIdEl = document.getElementById('selected-variant-id');
                var addBtn = document.getElementById('btn-add-to-cart');
                var buyBtn = document.getElementById('btn-order-now');
                var qtyInput = document.querySelector('[data-quantity-input]');

                function setStatus(html) { if (statusEl) statusEl.innerHTML = html; }

                function resolve() {
                    // Need every group chosen before a combo exists.
                    var allChosen = groupNames.every(function (g) { return selected[g]; });
                    if (!allChosen) {
                        if (addBtn) addBtn.disabled = true;
                        if (buyBtn) buyBtn.disabled = true;
                        if (variantIdEl) variantIdEl.value = '';
                        setStatus('<span class="inline-flex items-center gap-1 text-sm font-bold text-neutral-400 uppercase tracking-wider">{{ __('Select options') }}</span>');
                        return;
                    }
                    var key = groupNames.map(function (g) { return selected[g]; }).join('|');
                    var v = variantMap[key];
                    if (!v) {
                        if (addBtn) addBtn.disabled = true;
                        if (buyBtn) buyBtn.disabled = true;
                        if (variantIdEl) variantIdEl.value = '';
                        setStatus('<span class="inline-flex items-center gap-1.5 text-sm font-bold text-red-600 uppercase tracking-wider"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>{{ __('Unavailable combination') }}</span>');
                        return;
                    }
                    if (priceEl) priceEl.textContent = v.price_label;
                    if (variantIdEl) variantIdEl.value = v.id;
                    if (qtyInput) qtyInput.max = Math.max(1, v.stock);
                    if (v.stock > 0) {
                        if (addBtn) addBtn.disabled = false;
                        if (buyBtn) buyBtn.disabled = false;
                        setStatus('<span class="inline-flex items-center gap-1.5 text-sm font-bold text-emerald-600 uppercase tracking-wider"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>{{ __('In stock') }} (' + v.stock + ')</span>');
                    } else {
                        if (addBtn) addBtn.disabled = true;
                        if (buyBtn) buyBtn.disabled = true;
                        setStatus('<span class="inline-flex items-center gap-1.5 text-sm font-bold text-red-600 uppercase tracking-wider"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>{{ __('Out of stock') }}</span>');
                    }
                }

                picker.querySelectorAll('[data-option-group]').forEach(function (group) {
                    var name = group.getAttribute('data-option-group');
                    group.querySelectorAll('[data-option-value]').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            selected[name] = btn.getAttribute('data-option-value');
                            group.querySelectorAll('[data-option-value]').forEach(function (b) {
                                b.classList.remove('border-brand', 'bg-brand/5', 'text-brand', 'ring-1', 'ring-brand/10');
                                b.classList.add('border-neutral-200', 'text-neutral-700', 'bg-white');
                            });
                            btn.classList.add('border-brand', 'bg-brand/5', 'text-brand', 'ring-1', 'ring-brand/10');
                            btn.classList.remove('border-neutral-200', 'text-neutral-700', 'bg-white');
                            resolve();
                        });
                    });
                });
            // Sticky Mobile action bar visibility & state sync
            (function () {
                var bar = document.getElementById('sticky-action-bar');
                var mainBtn = document.getElementById('btn-order-now') || document.getElementById('btn-add-to-cart');
                var stickyBuyBtn = document.getElementById('btn-sticky-buy');
                var variantIdEl = document.getElementById('selected-variant-id');
                var picker = document.getElementById('variant-picker');

                if (!bar || !mainBtn) return;

                // Scroll listener
                window.addEventListener('scroll', function () {
                    var rect = mainBtn.getBoundingClientRect();
                    if (rect.bottom < 0) {
                        bar.classList.remove('translate-y-full');
                    } else {
                        bar.classList.add('translate-y-full');
                    }
                });

                // Buy handler
                if (stickyBuyBtn) {
                    stickyBuyBtn.addEventListener('click', function () {
                        if (picker && !variantIdEl.value) {
                            picker.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            picker.classList.add('ring-2', 'ring-brand/40', 'rounded-2xl', 'p-2', 'transition-all');
                            setTimeout(function () {
                                picker.classList.remove('ring-2', 'ring-brand/40', 'rounded-2xl', 'p-2');
                            }, 1500);
                            return;
                        }
                        var orderNowBtn = document.getElementById('btn-order-now');
                        if (orderNowBtn) {
                            orderNowBtn.click();
                        }
                    });
                }

                // Sync variant change with sticky bar price and button text
                var originalResolve = resolve;
                resolve = function () {
                    originalResolve();
                    var stickyPrice = document.getElementById('sticky-price');
                    var stickyText = document.getElementById('sticky-btn-text');

                    if (variantIdEl && variantIdEl.value) {
                        if (stickyText) stickyText.textContent = "{{ __('ORDER NOW') }}";
                        var variantMap = JSON.parse(picker.getAttribute('data-variant-map') || '{}');
                        var optionGroups = JSON.parse(picker.getAttribute('data-option-groups') || '{}');
                        var groupNames = Object.keys(optionGroups);
                        var key = groupNames.map(function (g) { return selected[g]; }).join('|');
                        var v = variantMap[key];
                        if (v && stickyPrice) {
                            stickyPrice.textContent = v.price_label;
                        }
                    } else {
                        if (stickyText && picker) stickyText.textContent = "{{ __('SELECT OPTIONS') }}";
                    }
                };
                // Pre-select first variant options if available
                groupNames.forEach(function (name) {
                    var firstBtn = picker.querySelector('[data-option-group="' + name + '"] [data-option-value]');
                    if (firstBtn) {
                        firstBtn.click();
                    }
                });
            })();
        })();
        </script>
    @endpush

    {{-- ─────────────── AI Virtual Try-On Modal ─────────────── --}}
    @if ($tryOnEnabled)
        <div id="tryon-modal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-white rounded-md w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-neutral-100 sticky top-0 bg-white">
                    <h3 class="font-bold text-ink flex items-center gap-2">
                        <i class="ph ph-sparkle text-purple-600"></i> {{ __('AI Virtual Try-On') }}
                    </h3>
                    <button type="button" id="tryon-close" class="w-8 h-8 rounded-full hover:bg-neutral-100 flex items-center justify-center text-neutral-500">
                        <i class="ph ph-x text-lg"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    <p class="text-sm text-[color:var(--color-muted)]">
                        {{ __('Upload a clear, front-facing photo of yourself to preview') }}
                        <span class="font-semibold text-ink">{{ $product->name }}</span>
                        {{ __('on you. Your photo is not stored.') }}
                    </p>

                    {{-- Upload --}}
                    <div id="tryon-upload-area">
                        <label for="tryon-photo" class="block border-2 border-dashed border-neutral-200 rounded-md p-6 text-center cursor-pointer hover:border-purple-400 transition">
                            <i class="ph ph-camera text-3xl text-neutral-400"></i>
                            <p class="text-sm font-semibold text-neutral-600 mt-2">{{ __('Choose a photo') }}</p>
                            <p class="text-xs text-neutral-400 mt-1">{{ __('JPG, PNG or WEBP · up to 8MB') }}</p>
                            <input type="file" id="tryon-photo" accept="image/jpeg,image/png,image/webp" class="hidden">
                        </label>
                        <div id="tryon-preview-wrap" class="hidden mt-3 relative">
                            <img id="tryon-preview" class="w-full max-h-64 object-contain rounded-md border border-neutral-100" alt="preview">
                            <button type="button" id="tryon-change" class="absolute top-2 right-2 bg-white/90 text-xs font-bold px-2 py-1 rounded shadow">{{ __('Change') }}</button>
                        </div>
                    </div>

                    @if ($tryOnCaptcha)
                        <div id="tryon-recaptcha" class="flex justify-center"></div>
                    @endif

                    <button type="button" id="tryon-generate" disabled
                        class="w-full bg-gradient-to-r from-purple-600 to-fuchsia-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-black py-3 rounded-md text-sm uppercase tracking-wider flex items-center justify-center gap-2">
                        <i class="ph ph-magic-wand"></i>
                        <span id="tryon-generate-text">{{ __('Generate Try-On') }}</span>
                    </button>

                    {{-- Result --}}
                    <div id="tryon-result" class="hidden">
                        <div class="border-t border-neutral-100 pt-4">
                            <p class="text-xs font-bold uppercase tracking-widest text-neutral-400 mb-2">{{ __('Your Try-On') }}</p>
                            <img id="tryon-result-img" class="w-full rounded-md border border-neutral-100" alt="try-on result">
                            <a id="tryon-download" download class="mt-3 inline-flex items-center gap-1.5 text-sm font-bold text-purple-600 hover:underline">
                                <i class="ph ph-download-simple"></i> {{ __('Download') }}
                            </a>
                        </div>
                    </div>

                    {{-- Honeypot (hidden from humans; bots tend to fill it) + form-open timestamp. --}}
                    <input type="text" id="tryon-website" name="website" tabindex="-1" autocomplete="off"
                        class="hidden" aria-hidden="true">
                    <input type="hidden" id="tryon-started-at" name="form_started_at" value="">

                    <p id="tryon-error" class="hidden text-sm text-red-600 bg-red-50 border border-red-100 rounded-md px-3 py-2"></p>
                    <p class="text-[11px] text-neutral-400 text-center">{{ __('AI previews are approximations and may differ from the actual product.') }}</p>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                (function () {
                    var modal = document.getElementById('tryon-modal');
                    if (!modal) return;
                    var openBtn = document.getElementById('btn-try-on');
                    var closeBtn = document.getElementById('tryon-close');
                    var fileInput = document.getElementById('tryon-photo');
                    var previewWrap = document.getElementById('tryon-preview-wrap');
                    var previewImg = document.getElementById('tryon-preview');
                    var changeBtn = document.getElementById('tryon-change');
                    var genBtn = document.getElementById('tryon-generate');
                    var genText = document.getElementById('tryon-generate-text');
                    var resultBox = document.getElementById('tryon-result');
                    var resultImg = document.getElementById('tryon-result-img');
                    var downloadLink = document.getElementById('tryon-download');
                    var errorBox = document.getElementById('tryon-error');

                    var endpoint = @json(route('shop.product.try-on', $product->slug));
                    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    var selectedFile = null;

                    var startedAtInput = document.getElementById('tryon-started-at');
                    var honeypotInput = document.getElementById('tryon-website');

                    // reCAPTCHA (rendered only when the extension is enabled).
                    var captchaEnabled = @json($tryOnCaptcha);
                    var captchaSiteKey = @json($tryOnCaptcha ? config('extension.recaptcha.site_key') : null);
                    var captchaWidgetId = null;

                    function renderCaptcha() {
                        if (!captchaEnabled || captchaWidgetId !== null) return;
                        // The reCAPTCHA script loads async — if it isn't ready yet, retry briefly.
                        if (!(window.grecaptcha && window.grecaptcha.render)) {
                            setTimeout(renderCaptcha, 300);
                            return;
                        }
                        captchaWidgetId = window.grecaptcha.render('tryon-recaptcha', { sitekey: captchaSiteKey });
                    }

                    function captchaToken() {
                        if (!captchaEnabled || captchaWidgetId === null || !window.grecaptcha) return '';
                        return window.grecaptcha.getResponse(captchaWidgetId) || '';
                    }

                    function resetCaptcha() {
                        if (captchaEnabled && captchaWidgetId !== null && window.grecaptcha) {
                            window.grecaptcha.reset(captchaWidgetId);
                        }
                    }

                    function open() {
                        modal.classList.remove('hidden'); modal.classList.add('flex');
                        document.body.style.overflow = 'hidden';
                        // Stamp when the form opened — used server-side to reject instant bot submits.
                        startedAtInput.value = Date.now();
                        renderCaptcha();
                    }
                    function close() { modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.style.overflow = ''; }
                    function showError(msg) { errorBox.textContent = msg; errorBox.classList.remove('hidden'); }
                    function clearError() { errorBox.classList.add('hidden'); }

                    if (openBtn) openBtn.addEventListener('click', open);
                    closeBtn.addEventListener('click', close);
                    modal.addEventListener('click', function (e) { if (e.target === modal) close(); });

                    fileInput.addEventListener('change', function () {
                        clearError();
                        var f = fileInput.files[0];
                        if (!f) return;
                        if (f.size > 8 * 1024 * 1024) { showError(@json(__('That photo is too large (max 8MB).'))); fileInput.value = ''; return; }
                        selectedFile = f;
                        previewImg.src = URL.createObjectURL(f);
                        previewWrap.classList.remove('hidden');
                        genBtn.disabled = false;
                    });

                    changeBtn.addEventListener('click', function () {
                        fileInput.value = ''; selectedFile = null;
                        previewWrap.classList.add('hidden');
                        genBtn.disabled = true;
                    });

                    genBtn.addEventListener('click', function () {
                        if (!selectedFile) return;
                        clearError();

                        var token = captchaToken();
                        if (captchaEnabled && !token) {
                            showError(@json(__('Please complete the verification first.')));
                            return;
                        }

                        resultBox.classList.add('hidden');
                        genBtn.disabled = true;
                        genText.textContent = @json(__('Generating… this can take a moment'));

                        var fd = new FormData();
                        fd.append('photo', selectedFile);
                        fd.append('website', honeypotInput.value);
                        fd.append('form_started_at', startedAtInput.value);
                        if (captchaEnabled) fd.append('recaptcha_token', token);

                        fetch(endpoint, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                            body: fd,
                        })
                        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
                        .then(function (res) {
                            if (!res.ok) {
                                showError(res.body.message || @json(__('Something went wrong. Please try again.')));
                                return;
                            }
                            resultImg.src = res.body.image_url;
                            downloadLink.href = res.body.image_url;
                            resultBox.classList.remove('hidden');
                            resultBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        })
                        .catch(function () { showError(@json(__('Network error. Please try again.'))); })
                        .finally(function () {
                            genBtn.disabled = false;
                            genText.textContent = @json(__('Generate Try-On'));
                            // A reCAPTCHA token is single-use — reset so the next try needs a fresh check.
                            resetCaptcha();
                        });
                    });
                })();
            </script>
            @if ($tryOnCaptcha)
                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            @endif
        @endpush
    @endif

    {{-- Sticky Mobile Action Bar --}}
    @if ($product->isInStock())
        <div id="sticky-action-bar" class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-neutral-100 p-4 z-40 flex items-center justify-between gap-4 lg:hidden shadow-2xl translate-y-full transition-transform duration-300">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-12 h-12 rounded-md overflow-hidden shrink-0 border border-neutral-100 bg-neutral-50">
                    <img src="{{ $mainImage ?? '' }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>
                <div class="min-w-0 leading-tight">
                    <div class="text-xs font-bold text-neutral-800 truncate">{{ $product->name }}</div>
                    <div id="sticky-price" class="text-sm font-black text-brand mt-0.5">{{ amountWithSymbol($product->displayPrice()) }}</div>
                </div>
            </div>
            <button type="button" id="btn-sticky-buy"
                class="bg-neutral-900 hover:bg-black text-white font-black py-3 px-6 rounded-md transition duration-200 text-xs tracking-wider uppercase shadow-md shrink-0 flex items-center gap-2">
                <i class="ph ph-lightning text-base"></i>
                <span id="sticky-btn-text">{{ $hasVariants ? __('SELECT OPTIONS') : __('ORDER NOW') }}</span>
            </button>
        </div>
    @endif

@endsection

{{-- Pre-fill the site-wide WhatsApp button with this product (see shop.partials.whatsapp-float). --}}
@section('wa_message'){{ __('Hi, I am interested in :product', ['product' => $product->name]) }}@overwrite
