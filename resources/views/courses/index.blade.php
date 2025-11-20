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

    <table class="w-full border">
        <thead>
        <tr class="bg-gray-200">
            <th class="p-2 border">ID</th>
            <th class="p-2 border">Название</th>
            <th class="p-2 border">Изображение</th>
            <th class="p-2 border">Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($courses as $course)
            <tr>
                <td class="p-2 border">{{ $course->id }}</td>
                <td class="p-2 border">{{ $course->title }}</td>
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
