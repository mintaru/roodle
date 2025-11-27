<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-800 bg-white antialiased">

<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0
            bg-gradient-to-br from-white via-blue-50 to-indigo-100 relative">

    <!-- Decorative elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-0 w-80 h-80 bg-blue-200 opacity-30 rounded-full
                    -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>

        <div class="absolute bottom-0 right-0 w-80 h-80 bg-purple-200 opacity-30 rounded-full
                    translate-x-1/2 translate-y-1/2 blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-xl
                overflow-hidden sm:rounded-2xl border border-gray-300">
        {{ $slot }}
    </div>

</div>

</body>
</html>
