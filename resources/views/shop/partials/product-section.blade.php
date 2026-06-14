{{-- Reusable grid product section: $title, $products (collection), $viewAll (url) --}}
@if ($products->isNotEmpty())
<section class="shop-container mt-14 sm:mt-16">

    {{-- Heading --}}
    <div class="flex items-end justify-between gap-4 mb-6">
        <div class="section-heading is-start mb-0">
            <h2>{{ $title }}</h2>
        </div>
        @if (isset($viewAll))
            <a href="{{ $viewAll }}" class="btn-outline shrink-0 hidden sm:inline-flex">
                {{ __('View All') }}
                <i class="ph ph-arrow-right text-sm"></i>
            </a>
        @endif
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">
        @foreach ($products as $product)
            <x-shop::product-card :product="$product" />
        @endforeach
    </div>

    {{-- View all (mobile) --}}
    @if (isset($viewAll))
        <div class="flex justify-center mt-8 sm:hidden">
            <a href="{{ $viewAll }}" class="btn-outline">
                {{ __('View All') }}
                <i class="ph ph-arrow-right text-sm"></i>
            </a>
        </div>
    @endif

</section>
@endif
