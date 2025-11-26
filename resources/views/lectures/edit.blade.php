<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование лекции</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Редактирование лекции</h1>

    @if($errors->any())
        <div class="p-3 bg-red-200 text-red-800 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.lectures.update', $lecture) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Название лекции</label>
            <input type="text" id="title" name="title" value="{{ old('title', $lecture->title) }}" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Введите название лекции">
        </div>

        <div>
            <label for="course" class="block text-sm font-medium text-gray-700 mb-2">Курс</label>
            <input type="text" id="course" disabled value="{{ $lecture->course->title }}" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-gray-500">
            <p class="text-sm text-gray-500 mt-1">Курс не может быть изменен</p>
        </div>

        <div>
            <label for="pdf" class="block text-sm font-medium text-gray-700 mb-2">PDF файл (опционально)</label>
            <p class="text-sm text-gray-600 mb-2">
                @if($lecture->pdf_path)
                    Текущий файл: <a href="{{ asset('storage/' . $lecture->pdf_path) }}" target="_blank" class="text-blue-600 hover:underline">Скачать</a>
                @else
                    Файл не загружен
                @endif
            </p>
            <input type="file" id="pdf" name="pdf" accept=".pdf" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Выберите PDF файл для замены">
            <p class="text-sm text-gray-500 mt-1">Оставьте пусто, если не хотите менять файл</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="from_page" class="block text-sm font-medium text-gray-700 mb-2">С какой страницы</label>
                <input type="number" id="from_page" name="from_page" min="1" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="1">
            </div>
            <div>
                <label for="to_page" class="block text-sm font-medium text-gray-700 mb-2">До какой страницы</label>
                <input type="number" id="to_page" name="to_page" min="1" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Последняя">
            </div>
        </div>
        <p class="text-sm text-gray-500">Эти параметры применяются только если загружается новый PDF файл</p>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Сохранить изменения</button>
            <a href="{{ route('admin.lectures.index') }}" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Отмена</a>
        </div>
    </form>
</div>

</body>
</html>
