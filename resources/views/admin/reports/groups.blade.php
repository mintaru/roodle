<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отчёт по группам</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Вернуться в админ-панель
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Отчёт по группам</h1>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-3 text-left">Название группы</th>
                    <th class="border p-3 text-center">Количество пользователей</th>
                    <th class="border p-3 text-center">Количество доступных курсов</th>
                    <th class="border p-3 text-center">Средний балл по тестам</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groups as $group)
                    <tr class="hover:bg-gray-50">
                        <td class="border p-3">
                            <a href="{{ route('admin.groups.show', $group['id']) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $group['name'] }}
                            </a>
                        </td>
                        <td class="border p-3 text-center">
                            <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                                {{ $group['users_count'] }}
                            </span>
                        </td>
                        <td class="border p-3 text-center">
                            <span class="inline-block bg-purple-100 text-purple-800 px-3 py-1 rounded-full">
                                {{ $group['courses_count'] }}
                            </span>
                        </td>
                        <td class="border p-3 text-center">
                            <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full font-semibold">
                                {{ $group['average_score'] }}%
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="border p-3 text-center text-gray-500">Нет данных о группах</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
