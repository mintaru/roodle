<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отчёт об активности пользователей</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Вернуться в админ-панель
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Отчёт об активности пользователей</h1>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-3 text-left">Имя пользователя</th>
                    <th class="border p-3 text-left">Логин</th>
                    <th class="border p-3 text-left">Последний вход</th>
                    <th class="border p-3 text-left">Группы</th>
                    <th class="border p-3 text-center">Тестов пройдено</th>
                    <th class="border p-3 text-center">Всего попыток</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="border p-3">{{ $user['name'] }}</td>
                        <td class="border p-3">{{ $user['username'] }}</td>
                        <td class="border p-3">{{ $user['last_login'] }}</td>
                        <td class="border p-3">{{ $user['groups'] ?: 'Не в группах' }}</td>
                        <td class="border p-3 text-center">{{ $user['tests_passed'] }}</td>
                        <td class="border p-3 text-center">{{ $user['total_attempts'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="border p-3 text-center text-gray-500">Нет данных о пользователях</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
