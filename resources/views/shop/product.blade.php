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
        $phone = config('application_info.company_info.phone');
        $whatsapp = preg_replace('/[^0-9]/', '', $phone);
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
        <nav class="text-xs text-[color:var(--color-muted)] mb-5">
            <a href="{{ route('home') }}" class="hover:text-[color:var(--color-brand)]">{{ __('Home') }}</a>
            <span class="mx-1">/</span>
            <a href="{{ route('shop.index') }}" class="hover:text-[color:var(--color-brand)]">{{ __('Shop') }}</a>
            <span class="mx-1">/</span><span class="text-[color:var(--color-ink)]">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            {{-- Gallery: vertical thumbnails + main image --}}
            <div class="flex gap-3">
                @if ($gallery->count() > 1)
                    <div class="flex flex-col gap-3 shrink-0">
                        @foreach ($gallery as $img)
                            <button type="button" data-thumb="{{ $img }}"
                                class="w-16 h-20 rounded-xl border border-[color:var(--color-line)] overflow-hidden hover:border-[color:var(--color-brand)] focus:border-[color:var(--color-brand)]">
                                <img src="{{ $img }}" alt="" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
                <div class="flex-1 rounded-2xl border border-[color:var(--color-line)] bg-[color:var(--color-image)] overflow-hidden aspect-[3/4] flex items-center justify-center">
                    @if ($mainImage)
                        <img id="main-product-image" src="{{ $mainImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <i class="ph ph-image text-7xl text-neutral-300"></i>
                    @endif
                </div>
            </div>

            {{-- Info --}}
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-[color:var(--color-ink)]">{{ $product->name }}</h1>

                <div class="mt-3 flex items-center gap-3">
                    <span id="product-price" class="text-2xl font-bold text-[color:var(--color-brand)]">{{ amountWithSymbol($product->displayPrice()) }}</span>
                    @if ($hasDiscount)
                        <span class="text-base text-[color:var(--color-muted)] line-through">{{ amountWithSymbol($product->compare_at_price) }}</span>
                    @endif
                </div>

                {{-- Variant option pickers --}}
                @if ($hasVariants)
                    <div id="variant-picker" class="mt-5 border-t border-[color:var(--color-line)] pt-4 space-y-4"
                        data-variant-map='@json($variantMap)' data-option-groups='@json($optionGroups)'>
                        @foreach ($optionGroups as $groupName => $values)
                            <div class="flex items-start justify-between gap-3">
                                <span class="text-sm text-[color:var(--color-muted)] pt-1.5">{{ __($groupName) }}</span>
                                <div class="flex flex-wrap items-center gap-2 justify-end" data-option-group="{{ $groupName }}">
                                    @foreach ($values as $val)
                                        <button type="button" data-option-value="{{ $val }}"
                                            class="px-3.5 py-1.5 text-sm rounded-full border border-[color:var(--color-line)] hover:border-[color:var(--color-brand)] transition">
                                            {{ $val }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif ($mainImage)
                    {{-- Color row (simple product) --}}
                    <div class="mt-5 flex items-center justify-between border-t border-[color:var(--color-line)] pt-4">
                        <span class="text-sm text-[color:var(--color-muted)]">{{ __('Color') }}</span>
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-12 rounded-md border-2 border-[color:var(--color-brand)] overflow-hidden">
                                <img src="{{ $mainImage }}" alt="" class="w-full h-full object-cover">
                            </span>
                            <span class="text-sm font-semibold text-[color:var(--color-brand)] uppercase">{{ $product->category?->name }}</span>
                        </div>
                    </div>
                @endif

                {{-- Stock status --}}
                <div class="mt-4 flex items-center justify-between border-t border-[color:var(--color-line)] pt-4">
                    <span class="text-sm text-[color:var(--color-muted)]">{{ __('Status') }}</span>
                    <span id="variant-status">
                        @if ($hasVariants)
                            <span class="inline-flex items-center gap-1 text-sm text-[color:var(--color-muted)]">{{ __('Select an option') }}</span>
                        @elseif ($product->isInStock())
                            <span class="inline-flex items-center gap-1 text-sm text-emerald-600"><i class="ph ph-check-circle"></i> {{ __('In stock') }} ({{ $product->effectiveStock() }})</span>
                        @else
                            <span class="inline-flex items-center gap-1 text-sm text-red-600"><i class="ph ph-x-circle"></i> {{ __('Out of stock') }}</span>
                        @endif
                    </span>
                </div>

                {{-- Quantity + actions --}}
                @if ($product->isInStock())
                    <div class="mt-6 flex flex-col sm:flex-row items-stretch gap-3">
                        <input type="hidden" id="selected-variant-id" value="">
                        <div data-qty-wrap class="flex items-center justify-between sm:justify-start border border-[color:var(--color-line)] rounded-full px-1 shrink-0">
                            <button type="button" data-step="-1" class="size-9 text-lg hover:text-[color:var(--color-brand)]">−</button>
                            <input type="number" data-quantity-input value="1" min="1" max="{{ $hasVariants ? 99 : $product->stock }}"
                                class="w-12 text-center py-2 focus:outline-none bg-transparent">
                            <button type="button" data-step="1" class="size-9 text-lg hover:text-[color:var(--color-brand)]">+</button>
                        </div>
                        <button type="button" data-add-to-cart="{{ route('shop.cart.add', $product->id) }}"
                            @if ($hasVariants) disabled @endif
                            class="flex-1 bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white font-medium py-3 px-6 rounded-full transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="ph ph-shopping-cart-simple"></i> {{ __('Add to Cart') }}
                        </button>
                        <a href="{{ route('shop.cart.add', $product->id) }}" data-buy-now="{{ route('shop.cart.add', $product->id) }}"
                            class="flex-1 text-center bg-[color:var(--color-ink)] hover:bg-black text-white font-medium py-3 px-6 rounded-full transition">
                            {{ __('Buy Now') }}
                        </a>
                    </div>
                @endif

                {{-- Meta grid --}}
                <div class="mt-6 grid grid-cols-2 gap-y-3 gap-x-6 text-sm border-t border-[color:var(--color-line)] pt-5">
                    <div>
                        <div class="text-[color:var(--color-muted)]">{{ __('Material') }}</div>
                        <div class="font-medium text-[color:var(--color-ink)]">{{ $product->name }}</div>
                    </div>
                    @if ($product->category)
                        <div>
                            <div class="text-[color:var(--color-muted)]">{{ __('Category') }}</div>
                            <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="font-medium text-[color:var(--color-brand)] hover:underline">{{ $product->category->name }}</a>
                        </div>
                    @endif
                    @if ($product->sku)
                        <div>
                            <div class="text-[color:var(--color-muted)]">{{ __('Reference SKU') }}</div>
                            <div class="font-medium text-[color:var(--color-ink)]">{{ $product->sku }}</div>
                        </div>
                    @endif
                </div>

                {{-- Contact cards --}}
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="tel:{{ $phone }}" class="flex items-center gap-3 border border-[color:var(--color-line)] rounded-xl p-4 hover:border-[color:var(--color-brand)] transition">
                        <span class="size-10 rounded-full bg-[color:var(--color-brand-soft)] text-[color:var(--color-brand)] flex items-center justify-center shrink-0"><i class="ph ph-phone-call text-xl"></i></span>
                        <span class="leading-tight">
                            <span class="block text-xs text-[color:var(--color-muted)]">{{ __('Expert Support') }}</span>
                            <span class="block font-semibold text-[color:var(--color-ink)]">{{ $phone }}</span>
                        </span>
                    </a>
                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="flex items-center gap-3 border border-[color:var(--color-line)] rounded-xl p-4 hover:border-[color:var(--color-brand)] transition">
                        <span class="size-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><i class="ph ph-whatsapp-logo text-xl"></i></span>
                        <span class="leading-tight">
                            <span class="block text-xs text-[color:var(--color-muted)]">{{ __('WhatsApp Order') }}</span>
                            <span class="block font-semibold text-[color:var(--color-ink)]">{{ $phone }}</span>
                        </span>
                    </a>
                </div>

                {{-- Share --}}
                <div class="mt-6 flex items-center gap-3">
                    <span class="text-sm font-medium text-[color:var(--color-ink)]">{{ __('Share') }}</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener" class="size-9 rounded-full bg-[#1877f2] text-white flex items-center justify-center"><i class="ph ph-facebook-logo"></i></a>
                    <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}" target="_blank" rel="noopener" class="size-9 rounded-full bg-[#1da1f2] text-white flex items-center justify-center"><i class="ph ph-twitter-logo"></i></a>
                    <a href="https://wa.me/?text={{ $shareUrl }}" target="_blank" rel="noopener" class="size-9 rounded-full bg-emerald-500 text-white flex items-center justify-center"><i class="ph ph-whatsapp-logo"></i></a>
                </div>
            </div>
        </div>

        {{-- Description --}}
        @if ($product->description)
            <section class="mt-12 max-w-4xl">
                <h2 class="text-lg font-bold text-[color:var(--color-ink)] border-l-4 border-[color:var(--color-brand)] pl-3 mb-4">{{ __('Description') }}</h2>
                <div class="prose prose-sm sm:prose max-w-none text-[color:var(--color-body)]">
                    {!! $product->description !!}
                </div>
            </section>
        @endif

        {{-- Related --}}
        @if ($related->isNotEmpty())
            <section class="mt-14">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-[color:var(--color-ink)] border-l-4 border-[color:var(--color-brand)] pl-3">{{ __('Related Products') }}</h2>
                    <a href="{{ route('shop.index', ['category' => $product->category?->slug]) }}" class="text-sm font-medium text-[color:var(--color-brand)] hover:underline whitespace-nowrap">{{ __('View All') }} <i class="ph ph-arrow-right"></i></a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach ($related as $item)
                        <x-shop::product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- You may also like --}}
        @if (isset($recommended) && $recommended->isNotEmpty())
            <section class="mt-14">
                <h2 class="text-xl font-bold text-[color:var(--color-ink)] border-l-4 border-[color:var(--color-brand)] pl-3 mb-5">{{ __('You may also like') }}</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
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
                var addBtn = document.querySelector('[data-add-to-cart]');
                var qtyInput = document.querySelector('[data-quantity-input]');

                function setStatus(html) { if (statusEl) statusEl.innerHTML = html; }

                function resolve() {
                    // Need every group chosen before a combo exists.
                    var allChosen = groupNames.every(function (g) { return selected[g]; });
                    if (!allChosen) {
                        if (addBtn) addBtn.disabled = true;
                        if (variantIdEl) variantIdEl.value = '';
                        setStatus('<span class="inline-flex items-center gap-1 text-sm text-[color:var(--color-muted)]">{{ __('Select an option') }}</span>');
                        return;
                    }
                    var key = groupNames.map(function (g) { return selected[g]; }).join('|');
                    var v = variantMap[key];
                    if (!v) {
                        if (addBtn) addBtn.disabled = true;
                        if (variantIdEl) variantIdEl.value = '';
                        setStatus('<span class="inline-flex items-center gap-1 text-sm text-red-600"><i class="ph ph-x-circle"></i> {{ __('Unavailable combination') }}</span>');
                        return;
                    }
                    if (priceEl) priceEl.textContent = v.price_label;
                    if (variantIdEl) variantIdEl.value = v.id;
                    if (qtyInput) qtyInput.max = Math.max(1, v.stock);
                    if (v.stock > 0) {
                        if (addBtn) addBtn.disabled = false;
                        setStatus('<span class="inline-flex items-center gap-1 text-sm text-emerald-600"><i class="ph ph-check-circle"></i> {{ __('In stock') }} (' + v.stock + ')</span>');
                    } else {
                        if (addBtn) addBtn.disabled = true;
                        setStatus('<span class="inline-flex items-center gap-1 text-sm text-red-600"><i class="ph ph-x-circle"></i> {{ __('Out of stock') }}</span>');
                    }
                }

                picker.querySelectorAll('[data-option-group]').forEach(function (group) {
                    var name = group.getAttribute('data-option-group');
                    group.querySelectorAll('[data-option-value]').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            selected[name] = btn.getAttribute('data-option-value');
                            group.querySelectorAll('[data-option-value]').forEach(function (b) {
                                b.classList.remove('border-[color:var(--color-brand)]', 'bg-[color:var(--color-brand-soft)]', 'text-[color:var(--color-brand)]', 'font-semibold');
                            });
                            btn.classList.add('border-[color:var(--color-brand)]', 'bg-[color:var(--color-brand-soft)]', 'text-[color:var(--color-brand)]', 'font-semibold');
                            resolve();
                        });
                    });
                });
            })();
        </script>
    @endpush
@endsection
