{{-- Tinted promotional band wrapping a product grid: $title, $eyebrow, $products, $viewAll --}}
@if ($products->isNotEmpty())
<section class="mt-14 mb-14 bg-brand/5 border-y border-line py-12 sm:py-16">
    <div class="shop-container">

        {{-- Heading --}}
        <div class="relative w-full flex flex-col items-center mb-8 text-center">
            <div class="section-heading mb-0">
                <span class="eyebrow">{{ $eyebrow ?? __('Featured') }}</span>
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
        @if (!empty($viewAll))
            <div class="flex justify-center mt-8">
                <a href="{{ $viewAll }}" class="btn-brand text-sm py-3 px-7">
                    {{ __('View All') }}
                    <i class="ph ph-arrow-right text-sm"></i>
                </a>
            </div>
        @endif

    </div>
</section>
@endif
