<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отчёты</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
        ← Вернуться в админ-панель
    </a>
    <h1 class="text-3xl font-bold mb-2 text-gray-800">Отчёты</h1>
    <p class="text-gray-600 mb-8">Выберите тип отчёта для просмотра</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('admin.reports.user-activity') }}" 
           class="p-6 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow hover:shadow-lg transform hover:scale-105 transition">
           <h2 class="text-lg font-bold mb-2">👤 Активность пользователей</h2>
           <p class="text-sm opacity-90">Последнее входы, группы, пройденные тесты</p>
        </a>

        <a href="{{ route('admin.reports.groups') }}" 
           class="p-6 bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg shadow hover:shadow-lg transform hover:scale-105 transition">
           <h2 class="text-lg font-bold mb-2">👥 Отчёт по группам</h2>
           <p class="text-sm opacity-90">Численность, курсы, успеваемость групп</p>
        </a>

        <a href="{{ route('admin.reports.courses') }}" 
           class="p-6 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow hover:shadow-lg transform hover:scale-105 transition">
           <h2 class="text-lg font-bold mb-2">📚 Отчёт по курсам</h2>
           <p class="text-sm opacity-90">Лекции, тесты, пользователи, результаты</p>
        </a>
    </div>
</div>

</body>
</html>
