{{-- Reusable grid product section: $title, $products (collection), $viewAll (url) --}}
@if ($products->isNotEmpty())
<section class="shop-container shop-section-gap">

    {{-- Heading --}}
    <div class="relative w-full flex flex-col items-center mb-6">
        <div class="section-heading mb-0">
            <span class="eyebrow">{{ $eyebrow ?? __('Discover') }}</span>
            <h2>{{ $title }}</h2>
        </div>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
        @foreach ($products as $product)
            <x-shop::product-card :product="$product" />
        @endforeach
    </div>

    {{-- View all --}}
    @if (isset($viewAll))
        <div class="flex justify-center mt-8">
            <a href="{{ $viewAll }}" class="btn-outline">
                {{ __('View All') }}
                <i class="ph ph-arrow-right text-sm"></i>
            </a>
        </div>
    @endif

</section>
@endif
