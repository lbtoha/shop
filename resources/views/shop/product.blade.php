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
        $phone = '01935100013';
        $whatsappNumber = '01710733329';
        $shareUrl = urlencode(request()->fullUrl());

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
                $comboKey = implode('|', array_values($attrs));
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
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
            {{-- Image Gallery Column --}}
            <div class="lg:col-span-7 flex flex-col sm:flex-row gap-4">
                {{-- Thumbnails --}}
                @if ($gallery->count() > 1)
                    <div class="flex sm:flex-col gap-3 order-2 sm:order-1 overflow-x-auto sm:overflow-visible shrink-0 py-1">
                        @foreach ($gallery as $img)
                            <button type="button" data-thumb="{{ $img }}"
                                class="w-16 h-20 rounded-xl border-2 border-neutral-100 overflow-hidden hover:border-brand transition focus:outline-none p-0.5 shrink-0">
                                <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-lg">
                            </button>
                        @endforeach
                    </div>
                @endif
                {{-- Primary Image --}}
                <div class="flex-1 rounded-[2rem] overflow-hidden bg-neutral-50 aspect-[3/4] shadow-sm order-1 sm:order-2">
                    @if ($mainImage)
                        <img id="main-product-image" src="{{ $mainImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-neutral-300">
                            <i class="ph ph-image text-7xl"></i>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Product Info & Action Column --}}
            <div class="lg:col-span-5">
                <h1 class="text-2xl sm:text-3xl font-black text-neutral-900 tracking-wide leading-tight">{{ $product->name }}</h1>

                {{-- Price Row --}}
                <div class="mt-4 flex items-baseline gap-3">
                    <span id="product-price" class="text-2xl sm:text-3xl font-black text-brand">{{ amountWithSymbol($product->displayPrice()) }}</span>
                    @if ($hasDiscount)
                        <span class="text-base text-neutral-400 line-through font-semibold">{{ amountWithSymbol($product->compare_at_price) }}</span>
                    @endif
                </div>

                {{-- Variant Option Pickers --}}
                @if ($hasVariants)
                    <div id="variant-picker" class="mt-6 border-t border-neutral-100 pt-5 space-y-5"
                        data-variant-map='@json($variantMap)' data-option-groups='@json($optionGroups)'>
                        @foreach ($optionGroups as $groupName => $values)
                            <div class="flex flex-col gap-2">
                                <div class="text-xs font-bold text-neutral-400 uppercase tracking-widest">
                                    {{ __($groupName) }}
                                </div>
                                <div class="flex items-center gap-3 flex-wrap" data-option-group="{{ $groupName }}">
                                    @foreach ($values as $val)
                                        <button type="button" data-option-value="{{ $val }}"
                                            class="px-4 h-12 rounded-xl border-2 border-neutral-200 text-sm font-black text-neutral-700 flex items-center justify-center hover:border-brand transition focus:outline-none">
                                            {{ $val }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Color Selector (for simple product without database variants) --}}
                    <div class="mt-6 border-t border-neutral-100 pt-5">
                        <div class="text-xs font-bold text-neutral-400 uppercase tracking-widest">
                            {{ __('COLOR') }}: <span class="text-neutral-800 font-extrabold ml-1">{{ $product->name === 'Fuchsia Azure Delight' ? 'PINK & BLUE' : 'MULTICOLOR' }}</span>
                        </div>
                        @if ($mainImage)
                            <div class="mt-2.5">
                                <button type="button" class="w-12 h-16 rounded-xl border-2 border-brand overflow-hidden focus:outline-none p-0.5">
                                    <img src="{{ $mainImage }}" alt="Color template" class="w-full h-full object-cover rounded-lg">
                                </button>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Stock Status Display --}}
                <div class="mt-4 flex items-center justify-between border-t border-neutral-100 pt-4">
                    <span class="text-xs font-bold text-neutral-400 uppercase tracking-widest">{{ __('AVAILABILITY') }}</span>
                    <span id="variant-status">
                        @if ($hasVariants)
                            <span class="inline-flex items-center gap-1 text-sm font-bold text-neutral-500">{{ __('Select options') }}</span>
                        @elseif ($product->isInStock())
                            <span class="inline-flex items-center gap-1.5 text-sm font-bold text-emerald-600">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                {{ __('In Stock') }} ({{ $product->effectiveStock() }})
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-sm font-bold text-red-600">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                {{ __('Out of Stock') }}
                            </span>
                        @endif
                    </span>
                </div>

                {{-- Add to Cart / Order Now actions --}}
                @if ($product->isInStock())
                    <div class="mt-8 pt-6 border-t border-neutral-100 flex flex-col sm:flex-row items-stretch gap-4">
                        <input type="hidden" id="selected-variant-id" name="variant_id" value="">

                        {{-- Quantity Stepper --}}
                        <div data-qty-wrap class="flex items-center justify-between border-2 border-neutral-100 rounded-xl px-2 shrink-0 bg-neutral-50/50">
                            <button type="button" data-step="-1" class="w-9 h-11 text-xl font-bold text-neutral-500 hover:text-brand transition">−</button>
                            <input type="number" data-quantity-input value="1" min="1" max="{{ $hasVariants ? 99 : $product->effectiveStock() }}"
                                class="w-10 text-center py-2 font-black text-neutral-800 focus:outline-none bg-transparent select-none">
                            <button type="button" data-step="1" class="w-9 h-11 text-xl font-bold text-neutral-500 hover:text-brand transition">+</button>
                        </div>

                        {{-- Add to Cart --}}
                        <button type="button" id="btn-add-to-cart" data-add-to-cart="{{ route('shop.cart.add', $product->id) }}"
                            @if ($hasVariants) disabled @endif
                            class="flex-1 bg-brand hover:bg-brand-dark text-white font-black py-3 px-6 rounded-xl transition duration-200 text-xs sm:text-sm tracking-wider uppercase flex items-center justify-center gap-2 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="ph ph-shopping-cart-simple text-base"></i>
                            <span>{{ __('ADD TO CART') }}</span>
                        </button>

                        {{-- Order Now --}}
                        <button type="button" id="btn-order-now" data-buy-now="{{ route('shop.cart.add', $product->id) }}"
                            @if ($hasVariants) disabled @endif
                            class="flex-1 bg-neutral-900 hover:bg-black text-white font-black py-3 px-6 rounded-xl transition duration-200 text-xs sm:text-sm tracking-wider uppercase flex items-center justify-center gap-2 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="ph ph-lightning text-base"></i>
                            <span>{{ __('ORDER NOW') }}</span>
                        </button>
                    </div>
                @else
                    <div class="mt-8 pt-6 border-t border-neutral-100">
                        <button disabled class="w-full bg-neutral-100 text-neutral-400 font-bold py-3 px-6 rounded-xl text-sm tracking-wider uppercase cursor-not-allowed">
                            {{ __('OUT OF STOCK') }}
                        </button>
                    </div>
                @endif

                {{-- Product Metadata Grid --}}
                <div class="mt-8 grid grid-cols-2 gap-y-4 gap-x-6 text-xs border-t border-neutral-100 pt-6">
                    <div>
                        <div class="text-neutral-400 font-bold uppercase tracking-widest mb-1">{{ __('MATERIAL') }}</div>
                        <div class="font-extrabold text-neutral-800">{{ $product->name }}</div>
                    </div>
                    @if ($product->category)
                        <div>
                            <div class="text-neutral-400 font-bold uppercase tracking-widest mb-1">{{ __('CATEGORY') }}</div>
                            <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}"
                                class="font-extrabold text-brand hover:underline uppercase">{{ $product->category->name }}</a>
                        </div>
                    @endif
                    @if ($product->sku)
                        <div class="col-span-2">
                            <div class="text-neutral-400 font-bold uppercase tracking-widest mb-1">{{ __('REFERENCE SKU') }}</div>
                            <div class="font-extrabold text-neutral-800 uppercase">{{ $product->sku }}</div>
                        </div>
                    @endif
                </div>

                {{-- Support & WhatsApp Buttons --}}
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="tel:{{ $phone }}" class="flex items-center gap-3.5 border border-neutral-100 rounded-2xl p-4 hover:border-brand/40 hover:shadow-sm transition bg-white">
                        <span class="w-10 h-10 rounded-full bg-brand/5 text-brand flex items-center justify-center shrink-0">
                            <i class="ph ph-phone text-lg font-bold"></i>
                        </span>
                        <span class="leading-tight">
                            <span class="block text-[10px] font-bold text-neutral-400 uppercase tracking-wider">{{ __('REPORT HELP') }}</span>
                            <span class="block font-black text-neutral-800 mt-0.5">{{ $phone }}</span>
                        </span>
                    </a>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber) }}" target="_blank" rel="noopener"
                        class="flex items-center gap-3.5 border border-neutral-100 rounded-2xl p-4 hover:border-emerald-300 hover:shadow-sm transition bg-white">
                        <span class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <i class="ph ph-whatsapp-logo text-lg font-bold"></i>
                        </span>
                        <span class="leading-tight">
                            <span class="block text-[10px] font-bold text-neutral-400 uppercase tracking-wider">{{ __('WHATSAPP ORDER') }}</span>
                            <span class="block font-black text-neutral-800 mt-0.5">{{ $whatsappNumber }}</span>
                        </span>
                    </a>
                </div>

                {{-- Share row --}}
                <div class="mt-8 flex items-center gap-4 border-t border-neutral-100 pt-6">
                    <span class="text-xs font-bold text-neutral-400 uppercase tracking-widest">{{ __('SHARE') }}</span>
                    <div class="flex items-center gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener"
                            class="w-8 h-8 rounded-full bg-[#1877f2] text-white flex items-center justify-center hover:scale-105 transition"><i class="ph ph-facebook-logo"></i></a>
                        <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}" target="_blank" rel="noopener"
                            class="w-8 h-8 rounded-full bg-[#1da1f2] text-white flex items-center justify-center hover:scale-105 transition"><i class="ph ph-twitter-logo"></i></a>
                        <a href="https://wa.me/?text={{ $shareUrl }}" target="_blank" rel="noopener"
                            class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center hover:scale-105 transition"><i class="ph ph-whatsapp-logo"></i></a>
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

        {{-- Related Products Section --}}
        @if ($related->isNotEmpty())
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
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
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
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
                    @foreach ($recommended as $item)
                        <x-shop::product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    @push('scripts')
        <script>
            // Thumbnail click -> swap main image
            document.querySelectorAll('[data-thumb]').forEach(function (b) {
                b.addEventListener('click', function () {
                    var main = document.getElementById('main-product-image');
                    if (main) main.src = b.getAttribute('data-thumb');
                });
            });

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
                        setStatus('<span class="inline-flex items-center gap-1 text-sm font-bold text-neutral-500">{{ __('Select options') }}</span>');
                        return;
                    }
                    var key = groupNames.map(function (g) { return selected[g]; }).join('|');
                    var v = variantMap[key];
                    if (!v) {
                        if (addBtn) addBtn.disabled = true;
                        if (buyBtn) buyBtn.disabled = true;
                        if (variantIdEl) variantIdEl.value = '';
                        setStatus('<span class="inline-flex items-center gap-1.5 text-sm font-bold text-red-600"><span class="w-2 h-2 rounded-full bg-red-500"></span>{{ __('Unavailable combination') }}</span>');
                        return;
                    }
                    if (priceEl) priceEl.textContent = v.price_label;
                    if (variantIdEl) variantIdEl.value = v.id;
                    if (qtyInput) qtyInput.max = Math.max(1, v.stock);
                    if (v.stock > 0) {
                        if (addBtn) addBtn.disabled = false;
                        if (buyBtn) buyBtn.disabled = false;
                        setStatus('<span class="inline-flex items-center gap-1.5 text-sm font-bold text-emerald-600"><span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>{{ __('In stock') }} (' + v.stock + ')</span>');
                    } else {
                        if (addBtn) addBtn.disabled = true;
                        if (buyBtn) buyBtn.disabled = true;
                        setStatus('<span class="inline-flex items-center gap-1.5 text-sm font-bold text-red-600"><span class="w-2 h-2 rounded-full bg-red-500"></span>{{ __('Out of stock') }}</span>');
                    }
                }

                picker.querySelectorAll('[data-option-group]').forEach(function (group) {
                    var name = group.getAttribute('data-option-group');
                    group.querySelectorAll('[data-option-value]').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            selected[name] = btn.getAttribute('data-option-value');
                            group.querySelectorAll('[data-option-value]').forEach(function (b) {
                                b.classList.remove('border-brand', 'bg-brand', 'text-white');
                            });
                            btn.classList.add('border-brand', 'bg-brand', 'text-white');
                            resolve();
                        });
                    });
                });
            })();
        </script>
    @endpush
@endsection
