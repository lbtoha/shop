<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('application_info.company_info.name', config('app.name')))</title>
    <link rel="icon" href="{{ asset(config('application_info.logo_favicon.favicon', '/favicon.ico')) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/shop/css/app.css', 'resources/shop/js/app.js'])
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
