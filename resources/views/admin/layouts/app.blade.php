<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('application_info.company_info.name', config('app.name', 'Laravel')) }}</title>
    <link rel="icon" href="{{ asset(config('application_info.logo_favicon.favicon', '/favicon.ico')) }}"> 

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">

    <script>
        if (localStorage.getItem("theme") === "dark") {
            document.documentElement.classList.add("dark");
        }
    </script>

    <!-- Dynamic Theme Colors -->
    @php
        $appInfo = array_merge(config('application_info'));
        $primaryColor = $appInfo['theme']['primary_color'] ?? '#6366f1';
    @endphp
    <style>
        :root {
            --primary-color: {{ $primaryColor }};
        }
    </style>

    <!-- Styles / Scripts -->
    @vite(['resources/admin/css/app.css'])
    @stack('styles')
</head>

<body class="bg-neutral-20 dark:bg-neutral-903 relative">
    <!-- toast notification -->
    <x-admin::toast />
    <!-- Navigation -->
    <x-admin.navigation />

    <!-- Main Content -->
    <main class="main-content">
        <div class="p-3 md:p-4 xxl:p-6">
            {{ $slot }}
        </div>
    </main>
    @vite(['resources/admin/js/app.js'])

    @stack('scripts')
</body>

</html>
