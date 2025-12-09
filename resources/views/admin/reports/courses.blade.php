<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отчёт по курсам</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Вернуться в админ-панель
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Отчёт по курсам</h1>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-3 text-left">Название курса</th>
                    <th class="border p-3 text-center">Количество лекций</th>
                    <th class="border p-3 text-center">Количество тестов</th>
                    <th class="border p-3 text-center">Связанных пользователей</th>
                    <th class="border p-3 text-center">Средний результат тестов</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                    <tr class="hover:bg-gray-50">
                        <td class="border p-3">
                            <a href="{{ route('courses.show', $course['id']) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $course['title'] }}
                            </a>
                        </td>
                        <td class="border p-3 text-center">
                            <span class="inline-block bg-orange-100 text-orange-800 px-3 py-1 rounded-full">
                                {{ $course['lectures_count'] }}
                            </span>
                        </td>
                        <td class="border p-3 text-center">
                            <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full">
                                {{ $course['tests_count'] }}
                            </span>
                        </td>
                        <td class="border p-3 text-center">
                            <span class="inline-block bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full">
                                {{ $course['users_count'] }}
                            </span>
                        </td>
                        <td class="border p-3 text-center">
                            <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-semibold">
                                {{ $course['average_score'] }}%
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="border p-3 text-center text-gray-500">Нет данных о курсах</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
