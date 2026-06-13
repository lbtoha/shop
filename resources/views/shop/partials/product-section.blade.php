{{-- Reusable homepage product section: $title, $subtitle?, $products (collection), $viewAll (url) --}}
@if ($products->isNotEmpty())
    <section class="shop-container mt-16">
        <!-- Centered Heading with Dividers -->
        <div class="flex items-center gap-4 mb-10">
            <div class="flex-grow border-t border-neutral-200/80"></div>
            <h2 class="text-xl sm:text-2xl font-bold text-neutral-800 tracking-wide text-center px-4">
                {{ $title }}
            </h2>
            <div class="flex-grow border-t border-neutral-200/80"></div>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <x-shop::product-card :product="$product" />
            @endforeach
        </div>

        <!-- Bottom Centered View All -->
        @if (isset($viewAll))
            <div class="flex justify-center mt-10">
                <a href="{{ $viewAll }}" class="inline-flex items-center gap-2 px-6 py-2.5 border border-neutral-200 hover:border-brand hover:text-brand rounded-2xl text-xs font-black text-neutral-600 transition-all duration-300 uppercase tracking-wider bg-white shadow-sm hover:shadow-md">
                    <span>{{ __('View All') }}</span>
                    <i class="ph ph-arrow-right text-sm"></i>
                </a>
            </div>
        @endif
    </section>
@endif
