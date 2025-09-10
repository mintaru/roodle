<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Курсы</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) <!-- если используешь Vite -->
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen">

<div class="container mx-auto py-8">

    @livewire('course-list')
</div>

@livewireScripts
</body>
</html>

