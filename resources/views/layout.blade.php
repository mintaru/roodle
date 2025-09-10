<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система Тестирования</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
<body class="bg-gray-100 text-gray-800">
<div class="container mx-auto px-4 py-8">
    <header class="mb-8">
        <a href="/"><h1 class="text-3xl font-bold text-center text-blue-700">Система Тестирования</h1></a>
    </header>
    <main>
        {!! $content !!}
    </main>
</div>
</body>
</html>'
