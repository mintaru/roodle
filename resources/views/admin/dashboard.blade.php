<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 p-8">

    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <a href="{{ route('home') }}">
            вернуться
        </a>
        <h1 class="text-3xl font-bold mb-2 text-gray-800">Админ-панель</h1>
        <p class="text-gray-600 mb-8">Добро пожаловать, <span class="font-semibold">{{ auth()->user()->name }}</span>!</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('admin.courses.index') }}"
                class="p-6 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow hover:shadow-lg transform hover:scale-105 transition">
                <h2 class="text-lg font-bold mb-2">📚 Список курсов</h2>
                <p class="text-sm opacity-90">Управляйте всеми курсами платформы</p>
            </a>

            <a href="{{ route('admin.lectures.index') }}"
                class="p-6 bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg shadow hover:shadow-lg transform hover:scale-105 transition">
                <h2 class="text-lg font-bold mb-2">📖 Список лекций</h2>
                <p class="text-sm opacity-90">Управляйте всеми лекциями</p>
            </a>

            <a href="{{ route('admin.tests.index') }}"
                class="p-6 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow hover:shadow-lg transform hover:scale-105 transition">
                <h2 class="text-lg font-bold mb-2">📝 Список тестов</h2>
                <p class="text-sm opacity-90">Управляйте всеми тестами</p>
            </a>

            <a href="{{ route('admin.question-bank.index') }}"
                class="p-6 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow hover:shadow-lg transform hover:scale-105 transition">
                <h2 class="text-lg font-bold mb-2">❓ Банк вопросов</h2>
                <p class="text-sm opacity-90">Управляйте всеми вопросами</p>
            </a>

            <a href="{{ route('admin.groups.index') }}"
                class="p-6 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow hover:shadow-lg transform hover:scale-105 transition">
                <h2 class="text-lg font-bold mb-2">👥 Список групп</h2>
                <p class="text-sm opacity-90">Управляйте группами обучающихся</p>
            </a>

            <a href="{{ route('admin.users.index') }}"
                class="p-6 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow hover:shadow-lg transform hover:scale-105 transition">
                <h2 class="text-lg font-bold mb-2">👤 Список пользователей</h2>
                <p class="text-sm opacity-90">Управляйте всеми пользователями</p>
            </a>

            <a href="{{ route('courses.archived') }}"
                class="p-6 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow hover:shadow-lg transform hover:scale-105 transition">
                <h2 class="text-lg font-bold mb-2">Архивированные курсы</h2>
            </a>

            <a href="{{ route('admin.reports.index') }}"
                class="p-6 bg-gradient-to-br from-red-500 to-red-600 text-white rounded-lg shadow hover:shadow-lg transform hover:scale-105 transition">
                <h2 class="text-lg font-bold mb-2">📊 Отчёты</h2>
                <p class="text-sm opacity-90">Просмотрите различные статистические отчёты</p>
            </a>
        </div>
    </div>

</body>

</html>
