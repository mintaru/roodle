<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои курсы - Roodle</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    @livewireStyles
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
</head>
<body>

    <!-- HEADER -->
    @include('components.menu')

    <!-- MAIN CONTENT -->
    @livewire('archived-course-list')

    <!-- SCRIPTS -->
    @livewireScripts

</body>
</html>
