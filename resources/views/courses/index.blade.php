<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список курсов</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Список курсов</h1>

    @if(session('success'))
        <div class="p-3 bg-green-200 text-green-800 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search Form -->
    <div class="mb-6 p-4 bg-gray-50 rounded border">
        <form method="GET" action="{{ route('admin.courses.index') }}" class="flex gap-3 items-end flex-wrap">
            <div class="flex-1 min-w-xs">
                <label class="block text-sm font-medium text-gray-700 mb-2">Искать по колонке:</label>
                <select name="search_column" id="search_column" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="title" {{ $searchColumn === 'title' ? 'selected' : '' }}>Название</option>
                    <option value="id" {{ $searchColumn === 'id' ? 'selected' : '' }}>ID</option>
                    <option value="author" {{ $searchColumn === 'author' ? 'selected' : '' }}>Автор</option>
                    <option value="description" {{ $searchColumn === 'description' ? 'selected' : '' }}>Описание</option>
                </select>
            </div>
            <div class="flex-1 min-w-xs">
                <label class="block text-sm font-medium text-gray-700 mb-2">Поисковый запрос:</label>
                <input type="text" name="search_value" placeholder="Введите текст для поиска..." value="{{ $searchValue }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Поиск</button>
            <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Очистить</a>
        </form>
    </div>

    <table class="w-full border">
        <thead>
        <tr class="bg-gray-200">
            <th class="p-2 border">ID</th>
            <th class="p-2 border">Название</th>
            <th class="p-2 border">Автор</th>
            <th class="p-2 border">Изображение</th>
            <th class="p-2 border">Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($courses as $course)
            <tr>
                <td class="p-2 border">{{ $course->id }}</td>
                <td class="p-2 border">{{ $course->title }}</td>
                <td class="p-2 border">{{ $course->author->name ?? 'Неизвестен' }}</td>
                <td class="p-2 border">
                    @if($course->image_path)
                        <img src="{{ asset('storage/' . $course->image_path) }}" alt="" class="w-16 h-16 object-cover">
                    @endif
                </td>
                <td class="p-2 border">
                    <a href="{{ route('courses.edit', $course) }}" class="text-blue-600 hover:underline">Редактировать</a>
                    <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Вы уверены, что хотите удалить этот курс?')">Удалить</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

</body>
</html>
