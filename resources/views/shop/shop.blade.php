@extends('shop.layouts.app')

@section('title', ($activeCategory->name ?? __('Shop')) . ' — ' . config('application_info.company_info.name'))

@section('content')
<div class="shop-container py-6 sm:py-10">

    {{-- ── Breadcrumb ───────────────────────────────────────── --}}
    <nav class="flex items-center gap-1.5 text-xs text-muted mb-5">
        <a href="{{ route('home') }}" class="hover:text-brand transition-colors">{{ __('Home') }}</a>
        <i class="ph ph-caret-right text-[10px] text-subtle"></i>
        <a href="{{ route('shop.index') }}" class="hover:text-brand transition-colors">{{ __('Shop') }}</a>
        @if ($activeCategory)
            <i class="ph ph-caret-right text-[10px] text-subtle"></i>
            <span class="text-ink font-medium">{{ $activeCategory->name }}</span>
        @endif
    </nav>

    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">

        {{-- ── Sidebar ──────────────────────────────────────── --}}
        <aside class="w-full lg:w-60 xl:w-64 shrink-0">

            {{-- Mobile filter toggle --}}
            <button type="button" id="filter-toggle"
                class="lg:hidden w-full flex items-center justify-between gap-2 bg-white border border-line rounded-xl px-4 py-3 text-sm font-semibold text-ink mb-3 shadow-sm">
                <span class="flex items-center gap-2">
                    <i class="ph ph-faders text-brand text-base"></i>
                    {{ __('Filter & Categories') }}
                </span>
                <i class="ph ph-caret-down text-muted text-sm transition-transform duration-200" id="filter-toggle-icon"></i>
            </button>

            {{-- Sidebar panel --}}
            <div id="filter-panel" class="hidden lg:block space-y-4">

                {{-- Category filter --}}
                <div class="bg-white rounded-2xl border border-line shadow-sm overflow-hidden">
                    <div class="flex items-center gap-2 px-4 py-3.5 border-b border-line-soft">
                        <i class="ph ph-squares-four text-brand text-base"></i>
                        <h3 class="text-sm font-bold text-ink">{{ __('Categories') }}</h3>
                    </div>
                    <ul class="p-2 space-y-0.5">
                        <li>
                            <a href="{{ route('shop.index') }}"
                               class="sidebar-cat-link {{ !$activeCategory ? 'active' : '' }}">
                                <span>{{ __('All Products') }}</span>
                                <span class="text-[11px] font-semibold opacity-60">{{ $products->total() }}</span>
                            </a>
                        </li>
                        @foreach ($categories as $cat)
                            <li>
                                <a href="{{ route('shop.index', ['category' => $cat->slug]) }}"
                                   class="sidebar-cat-link {{ optional($activeCategory)->id === $cat->id ? 'active' : '' }}">
                                    <span>{{ $cat->name }}</span>
                                    <span class="text-[11px] font-semibold opacity-60">{{ $cat->products_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Price range note --}}
                <div class="bg-brand-soft border border-brand/10 rounded-2xl p-4 text-center">
                    <i class="ph ph-hand-coins text-brand text-2xl mb-1 block"></i>
                    <p class="text-xs font-semibold text-brand">{{ __('Cash on Delivery') }}</p>
                    <p class="text-[11px] text-brand/70 mt-0.5">{{ __('Pay when your order arrives') }}</p>
                </div>
            </div>
        </aside>

        {{-- ── Product grid ─────────────────────────────────── --}}
        <div class="flex-1 min-w-0">

            {{-- Toolbar --}}
            <div class="flex items-center justify-between gap-3 mb-5">
                <div>
                    <h1 class="text-lg sm:text-xl font-bold text-ink leading-tight">
                        {{ $activeCategory->name ?? __('All Products') }}
                    </h1>
                    <p class="text-xs text-muted mt-0.5">
                        {{ number_format($products->total()) }} {{ __('products found') }}
                    </p>
                </div>

                <form method="GET" action="{{ route('shop.index') }}" class="shrink-0">
                    @if (request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                    @if (request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    <select name="sort" onchange="this.form.submit()"
                        class="border border-line rounded-xl py-2 pl-3 pr-8 text-xs sm:text-sm font-medium text-ink bg-white focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand/10 transition-all appearance-none cursor-pointer shadow-sm">
                        <option value="newest"     @selected(!request('sort') || request('sort') === 'newest')>{{ __('Newest First') }}</option>
                        <option value="oldest"     @selected(request('sort') === 'oldest')>{{ __('Oldest First') }}</option>
                        <option value="price_low"  @selected(request('sort') === 'price_low')>{{ __('Price: Low → High') }}</option>
                        <option value="price_high" @selected(request('sort') === 'price_high')>{{ __('Price: High → Low') }}</option>
                    </select>
                </form>
            </div>

            {{-- Active search/filter badge --}}
            @if (request('search'))
                <div class="flex items-center gap-2 mb-4">
                    <span class="inline-flex items-center gap-2 bg-brand-soft border border-brand/20 text-brand text-xs font-semibold px-3 py-1.5 rounded-full">
                        <i class="ph ph-magnifying-glass text-sm"></i>
                        "{{ request('search') }}"
                        <a href="{{ route('shop.index', array_filter(['category' => request('category'), 'sort' => request('sort')])) }}"
                           class="hover:text-brand-dark transition-colors">
                            <i class="ph ph-x text-xs"></i>
                        </a>
                    </span>
                </div>
            @endif

            {{-- Grid --}}
            @if ($products->isEmpty())
                <div class="flex flex-col items-center justify-center bg-white border border-line rounded-2xl py-20 px-8 text-center shadow-sm">
                    <div class="w-16 h-16 rounded-2xl bg-canvas flex items-center justify-center mb-4">
                        <i class="ph ph-package text-3xl text-muted"></i>
                    </div>
                    <h3 class="text-base font-bold text-ink mb-1">{{ __('No products found') }}</h3>
                    <p class="text-sm text-muted mb-5">{{ __('Try adjusting your filters or search terms.') }}</p>
                    <a href="{{ route('shop.index') }}" class="btn-brand text-xs py-2.5 px-5">
                        {{ __('Browse All Products') }}
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
                    @foreach ($products as $product)
                        <x-shop::product-card :product="$product" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Mobile filter toggle
    const filterToggle = document.getElementById('filter-toggle');
    const filterPanel  = document.getElementById('filter-panel');
    const toggleIcon   = document.getElementById('filter-toggle-icon');
    if (filterToggle && filterPanel) {
        filterToggle.addEventListener('click', () => {
            const isHidden = filterPanel.classList.toggle('hidden');
            toggleIcon?.classList.toggle('rotate-180', !isHidden);
        });
    }
</script>
@endpush
@endsection
