{{-- Dispatches a resolved home section to the partial for its layout. --}}
@props(['section'])

@php
    $partials = [
        'grid' => 'shop.partials.product-section',
        'slider' => 'shop.partials.product-slider-section',
        'carousel' => 'shop.partials.product-carousel-section',
        'banner' => 'shop.partials.product-banner-section',
    ];
    $partial = $partials[$section['layout']] ?? 'shop.partials.product-section';
@endphp

@include($partial, [
    'title' => $section['title'],
    'eyebrow' => $section['eyebrow'],
    'products' => $section['products'],
    'viewAll' => $section['viewAll'],
    'uid' => $section['id'],
])
