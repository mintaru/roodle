<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование лекции</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <div class="mb-4">
        <x-back-button :url="route('lectures.show', ['course' => $lecture->course, 'lecture' => $lecture])" text="К лекции" />
    </div>
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

    <form action="{{ route('admin.lectures.update', $lecture) }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="lecture-edit-form">
        @csrf
        @method('PUT')
        <input type="hidden" name="content_source" id="content-source-hidden" value="{{ ($lecture->content_type ?? 'text') === 'html' ? 'manual' : 'pdf' }}">

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
            <label class="block text-sm font-medium text-gray-700 mb-2">Способ контента</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="content_source_radio" value="manual" {{ ($lecture->content_type ?? 'text') === 'html' ? 'checked' : '' }}>
                    <span>Текст вручную</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="content_source_radio" value="pdf" {{ ($lecture->content_type ?? 'text') !== 'html' ? 'checked' : '' }}>
                    <span>PDF файл</span>
                </label>
            </div>
        </div>

        <div id="manual-content-block" style="{{ ($lecture->content_type ?? 'text') !== 'html' ? 'display: none;' : '' }}">
            <label for="content-input" class="block text-sm font-medium text-gray-700 mb-2">Текст лекции</label>
            <input id="content-input" type="hidden" name="content" value="{{ old('content', $lecture->content ?? '') }}">
            <trix-editor input="content-input" class="min-h-[300px] border border-gray-300 rounded p-3"></trix-editor>
        </div>

        <div id="pdf-block" style="{{ ($lecture->content_type ?? 'text') === 'html' ? 'display: none;' : '' }}">
            <label for="pdf" class="block text-sm font-medium text-gray-700 mb-2">PDF файл</label>
            <p class="text-sm text-gray-600 mb-2">
                @if($lecture->pdf_path)
                    Текущий файл: <a href="{{ asset('storage/' . $lecture->pdf_path) }}" target="_blank" class="text-blue-600 hover:underline">Скачать</a>
                @else
                    Файл не загружен
                @endif
            </p>
            <input type="file" id="pdf" name="pdf" accept=".pdf" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="from_page" class="block text-sm font-medium text-gray-700 mb-2">С какой страницы</label>
                    <input type="number" id="from_page" name="from_page" min="1" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="1">
                </div>
                <div>
                    <label for="to_page" class="block text-sm font-medium text-gray-700 mb-2">До какой страницы</label>
                    <input type="number" id="to_page" name="to_page" min="1" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Последняя">
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-1">Параметры страниц применяются только при загрузке нового PDF</p>
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Сохранить изменения</button>
            <a href="{{ route('admin.lectures.index') }}" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Отмена</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const manualBlock = document.getElementById('manual-content-block');
        const pdfBlock = document.getElementById('pdf-block');
        const hiddenSource = document.getElementById('content-source-hidden');
        const radios = document.querySelectorAll('input[name="content_source_radio"]');

        function toggleBlocks() {
            const source = document.querySelector('input[name="content_source_radio"]:checked').value;
            hiddenSource.value = source;
            manualBlock.style.display = source === 'manual' ? 'block' : 'none';
            pdfBlock.style.display = source === 'manual' ? 'none' : 'block';
        }
        radios.forEach(r => r.addEventListener('change', toggleBlocks));
    });
</script>
</body>
</html>
