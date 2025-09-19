<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать курс</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Редактировать курс</h1>

    @if ($errors->any())
        <div class="p-3 bg-red-200 text-red-800 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('courses.update', $course) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block font-medium">Название курса *</label>
            <input type="text" name="title" value="{{ old('title', $course->title) }}" class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block font-medium">Описание *</label>
            <textarea name="description" rows="4" class="w-full border rounded p-2">{{ old('description', $course->description) }}</textarea>
        </div>


        <div>
            <label class="block font-medium">Текущее изображение</label>
            @if($course->image_path)
                <img src="{{ asset('storage/' . $course->image_path) }}" alt="" class="w-24 h-24 object-cover mb-2">
            @endif
            <input type="file" name="image_path" class="w-full">
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Обновить
        </button>
    </form>
</div>

</body>
</html>
