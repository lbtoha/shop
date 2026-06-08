@extends('shop.layouts.app')

@section('title', ($activeCategory->name ?? __('Shop')) . ' — ' . config('application_info.company_info.name'))

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        {{-- Breadcrumb / heading --}}
        <div class="mb-6">
            <nav class="text-xs text-[color:var(--color-muted)] mb-1">
                <a href="{{ route('home') }}" class="hover:text-[color:var(--color-brand)]">{{ __('Home') }}</a>
                <span class="mx-1">/</span>
                <a href="{{ route('shop.index') }}" class="hover:text-[color:var(--color-brand)]">{{ __('Shop') }}</a>
                @if ($activeCategory)
                    <span class="mx-1">/</span><span>{{ $activeCategory->name }}</span>
                @endif
            </nav>
            <h1 class="text-2xl font-bold text-ink">{{ $activeCategory->name ?? __('All Products') }}</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Sidebar --}}
            <aside class="lg:col-span-1">
                <h3 class="font-semibold mb-3 text-ink">{{ __('Categories') }}</h3>
                <ul class="space-y-1 text-sm">
                    <li>
                        <a href="{{ route('shop.index') }}"
                            class="block py-1.5 px-2 rounded {{ ! $activeCategory ? 'bg-[color:var(--color-brand)] text-white' : 'hover:bg-white' }}">
                            {{ __('All Products') }}
                        </a>
                    </li>
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                                class="flex items-center justify-between py-1.5 px-2 rounded {{ optional($activeCategory)->id === $category->id ? 'bg-[color:var(--color-brand)] text-white' : 'hover:bg-white' }}">
                                <span>{{ $category->name }}</span>
                                <span class="text-xs opacity-70">{{ $category->products_count }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </aside>

            {{-- Product grid --}}
            <div class="lg:col-span-3">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-[color:var(--color-muted)]">{{ $products->total() }} {{ __('products') }}</p>
                    <form method="GET" action="{{ route('shop.index') }}">
                        @if (request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                        @if (request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                        <select name="sort" onchange="this.form.submit()"
                            class="border border-neutral-200 rounded py-1.5 px-3 text-sm bg-white focus:outline-none">
                            <option value="newest" @selected(request('sort') === 'newest' || ! request('sort'))>{{ __('Newest') }}</option>
                            <option value="oldest" @selected(request('sort') === 'oldest')>{{ __('Oldest') }}</option>
                            <option value="price_low" @selected(request('sort') === 'price_low')>{{ __('Price: Low to High') }}</option>
                            <option value="price_high" @selected(request('sort') === 'price_high')>{{ __('Price: High to Low') }}</option>
                        </select>
                    </form>
                </div>

                @if ($products->isEmpty())
                    <div class="bg-white border border-neutral-100 rounded p-12 text-center text-[color:var(--color-muted)]">
                        <i class="ph ph-package text-5xl mb-3 block"></i>
                        {{ __('No products found.') }}
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach ($products as $product)
                            <x-shop::product-card :product="$product" />
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
