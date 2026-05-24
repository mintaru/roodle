<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Прохождение теста</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @yield('head')
    <style>
        body { font-family: "Inter", sans-serif; }
        .card {
            @apply bg-white rounded-xl shadow-md p-6 sm:p-8;
        }
        .btn {
            @apply inline-block px-6 py-2.5 rounded-lg shadow-md font-semibold text-white transition-transform transform hover:scale-105;
        }
        .btn-primary {
            @apply bg-blue-600 hover:bg-blue-700;
        }
        .btn-secondary {
            @apply bg-gray-500 hover:bg-gray-600;
        }
        .btn-green {
            @apply bg-green-600 hover:bg-green-700;
        }
    </style>
</head>
<body>
    @include("components.menu")

    <div>
        @if(isset($content))

            {!! $content !!}
        @else
            @yield('content')
        @endif
    </div>
</body>
</html>
