<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link href="{{ asset('css/tailwind.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <script>
        if (localStorage.getItem('dark-mode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>

    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
    @yield('head')
    <style>
        body {
            font-family: "Inter", sans-serif;
            padding:0;
        }

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
    @include('components.menu')

    <div>
        @if (isset($content))
            {!! $content !!}
        @else
            @yield('content')
        @endif
    </div>
</body>

</html>
