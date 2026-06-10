{{-- Reusable homepage product section: $title, $subtitle?, $products (collection), $viewAll (url) --}}
@if ($products->isNotEmpty())
    <section class="shop-container mt-16">
        <div class="relative mb-8 border-b border-neutral-100 pb-3 flex justify-between items-end">
            <h2 class="text-xl sm:text-2xl font-black text-neutral-900 tracking-wider uppercase inline-block relative">
                {{ $title }}
                <span class="absolute bottom-[-13px] left-0 w-16 h-0.5 bg-brand"></span>
            </h2>
            <a href="{{ $viewAll }}" class="text-xs font-bold text-brand hover:text-brand-dark hover:underline uppercase tracking-wider flex items-center gap-1">
                <span>{{ __('VIEW ALL') }}</span>
                <i class="ph ph-arrow-right text-sm"></i>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <x-shop::product-card :product="$product" />
            @endforeach
        </div>
    </section>
@endif
