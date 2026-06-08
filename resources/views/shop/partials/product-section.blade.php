{{-- Reusable homepage product section: $title, $products (collection), $viewAll (url) --}}
@if ($products->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 mt-12">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl sm:text-2xl font-bold text-ink">{{ $title }}</h2>
            <a href="{{ $viewAll }}" class="text-sm font-medium text-[color:var(--color-brand)] hover:underline">
                {{ __('View All') }} <i class="ph ph-arrow-right"></i>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($products as $product)
                <x-shop::product-card :product="$product" />
            @endforeach
        </div>
    </section>
@endif
