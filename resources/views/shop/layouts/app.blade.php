<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('application_info.company_info.name', config('app.name')))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/shop/css/app.css', 'resources/shop/js/app.js'])
</head>

<body class="min-h-screen flex flex-col">
    @include('shop.partials.header')

    <main class="flex-1">
        @if (session('success'))
            <div class="max-w-7xl mx-auto px-4 mt-4">
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="max-w-7xl mx-auto px-4 mt-4">
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @include('shop.partials.footer')
</body>

</html>
