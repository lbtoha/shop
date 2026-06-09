{{-- Reusable homepage product section: $title, $subtitle?, $products (collection), $viewAll (url) --}}
@if ($products->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 mt-12">
        <div class="flex items-end justify-between mb-5">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-[color:var(--color-ink)]">{{ $title }}</h2>
                @if (! empty($subtitle))
                    <p class="text-sm text-[color:var(--color-muted)] mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            <a href="{{ $viewAll }}" class="text-sm font-medium text-[color:var(--color-brand)] hover:underline whitespace-nowrap">
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
