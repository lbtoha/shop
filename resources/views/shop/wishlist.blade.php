@extends('shop.layouts.app')

@section('title', __('My Wishlist') . ' — ' . config('application_info.company_info.name'))

@section('content')
<div class="shop-container py-6 sm:py-10">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs text-muted mb-5">
        <a href="{{ route('home') }}" class="hover:text-brand transition-colors">{{ __('Home') }}</a>
        <i class="ph ph-caret-right text-[10px] text-subtle"></i>
        <span class="text-ink font-medium">{{ __('Wishlist') }}</span>
    </nav>

    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-ink leading-tight">
            {{ __('My Wishlist') }}
        </h1>
        <p class="text-xs text-muted mt-1">
            {{ __('Keep track of products you love') }}
        </p>
    </div>

    {{-- Grid or Empty State --}}
    @if ($items->isEmpty())
        <div class="flex flex-col items-center justify-center bg-white border border-line rounded-2xl py-20 px-8 text-center shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-brand-soft flex items-center justify-center mb-4">
                <i class="ph ph-heart text-3xl text-brand"></i>
            </div>
            <h3 class="text-base font-bold text-ink mb-1">{{ __('Your wishlist is empty') }}</h3>
            <p class="text-sm text-muted mb-5">{{ __('Tap the heart icon on any product to add it here.') }}</p>
            <a href="{{ route('shop.index') }}" class="py-3 px-6 rounded-xl bg-brand hover:bg-brand-dark text-white text-xs font-semibold tracking-wide transition-colors">
                {{ __('Browse Products') }}
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach ($items as $product)
                <x-shop::product-card :product="$product" />
            @endforeach
        </div>
    @endif
</div>
@endsection
