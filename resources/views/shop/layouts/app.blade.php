<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Metadata -->
    <title>@yield('title', config('seo.title', config('application_info.company_info.name', config('app.name'))))</title>
    <meta name="description" content="@yield('meta_description', config('seo.description', ''))">
    <meta name="keywords" content="{{ config('seo.keywords', '') }}">
    @if(config('seo.author'))
        <meta name="author" content="{{ config('seo.author') }}">
    @endif
    @if(config('seo.robots'))
        <meta name="robots" content="{{ config('seo.robots') }}">
    @endif
    @if(config('seo.canonical_link'))
        <link rel="canonical" href="{{ config('seo.canonical_link') }}">
    @endif

    <!-- Custom Meta Tags (e.g. Facebook Domain Verification, search console keys) -->
    @foreach (config('seo.meta', []) as $meta)
        @if (!empty($meta['name']))
            <meta name="{{ $meta['name'] }}" content="{{ $meta['content'] }}">
        @elseif (!empty($meta['property']))
            <meta property="{{ $meta['property'] }}" content="{{ $meta['content'] }}">
        @endif
    @endforeach

    <!-- OpenGraph Metadata -->
    @hasSection('og_meta')
        @yield('og_meta')
    @else
        @if (config('seo.openGraph'))
            @foreach (config('seo.openGraph', []) as $key => $value)
                @if ($value)
                    <meta property="og:{{ $key }}" content="{{ $value }}">
                @endif
            @endforeach
        @endif
    @endif

    <!-- Twitter Metadata -->
    @hasSection('twitter_meta')
        @yield('twitter_meta')
    @else
        @if (config('seo.twitter'))
            @foreach (config('seo.twitter', []) as $key => $value)
                @if ($value)
                    <meta name="twitter:{{ $key }}" content="{{ $value }}">
                @endif
            @endforeach
        @endif
    @endif

    <!-- Structured Data (JSON-LD) -->
    @if (config('seo.structured_data.script.content'))
        <script type="application/ld+json">
            {!! config('seo.structured_data.script.content') !!}
        </script>
    @endif

    <link rel="icon" href="{{ asset(config('application_info.logo_favicon.favicon', '/favicon.ico')) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/shop/css/app.css', 'resources/shop/js/app.js'])
    @include('shop.partials.tracking-pixels')
</head>

<body class="min-h-screen flex flex-col overflow-x-hidden pb-[60px] lg:pb-0">
    @include('shop.partials.header')

    <main class="flex-1">
        @if (session('success'))
            <div class="shop-container mt-4">
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-md">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="shop-container mt-4">
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @include('shop.partials.footer')

    @include('shop.partials.cart-drawer')

    @include('shop.partials.whatsapp-float')

    @include('shop.partials.cart-float')

    @include('shop.partials.mobile-nav')

    @include('shop.partials.mobile-menu')

    @stack('scripts')
</body>

</html>
