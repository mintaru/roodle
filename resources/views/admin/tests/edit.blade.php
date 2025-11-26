<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование теста</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Редактирование теста</h1>

    @if($errors->any())
        <div class="p-3 bg-red-200 text-red-800 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.tests.update', $test) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Название теста</label>
            <input type="text" id="title" name="title" value="{{ old('title', $test->title) }}" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Введите название теста">
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Описание</label>
            <textarea id="description" name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Введите описание теста">{{ old('description', $test->description) }}</textarea>
        </div>

        <div>
            <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Курс</label>
            <select id="course_id" name="course_id" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Выберите курс --</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ old('course_id', $test->course_id) == $course->id ? 'selected' : '' }}>
                        {{ $course->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="max_attempts" class="block text-sm font-medium text-gray-700 mb-2">Максимум попыток</label>
            <div class="flex gap-4 items-center">
                <input type="number" id="max_attempts" name="max_attempts" min="0" value="{{ old('max_attempts', $test->max_attempts) }}" class="flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0 = неограниченно">
                <span class="text-sm text-gray-600">0 = неограниченно</span>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="bg-blue-50 p-4 rounded mb-4">
                <p class="text-sm font-medium text-gray-700">Информация о тесте:</p>
                <ul class="text-sm text-gray-600 mt-2 space-y-1">
                    <li><strong>ID:</strong> {{ $test->id }}</li>
                    <li><strong>Вопросов в тесте:</strong> {{ $test->questions->count() }}</li>
                    <li><strong>Всего попыток:</strong> {{ $test->attempts->count() }}</li>
                    <li><strong>Создан:</strong> {{ $test->created_at->format('d.m.Y H:i') }}</li>
                </ul>
            </div>
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Сохранить изменения</button>
            <a href="{{ route('admin.tests.index') }}" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Отмена</a>
        </div>
    </form>
</div>

</body>
</html>
