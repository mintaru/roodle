<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создание курса</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <div class="mb-4">
        <x-back-button :url="route('home')" text="К курсам" />
    </div>
    <h1 class="text-2xl font-bold mb-4">Создать курс</h1>

    @if(session('success'))
        <div class="p-3 bg-green-200 text-green-800 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="p-3 bg-red-200 text-red-800 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block font-medium">Название курса *</label>
            <input type="text" name="title" value="{{ old('title') }}"
                   class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block font-medium">Описание *</label>
            <textarea name="description" rows="4"
                      class="w-full border rounded p-2">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block font-medium">Изображение</label>
            <input type="file" name="image_path" class="w-full">
        </div>

        <div>
            <label class="block font-medium">Доступен с:</label>
            <input type="datetime-local" name="period_start"
                   class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block font-medium">Доступен до:</label>
            <input type="datetime-local" name="period_end"
                   class="w-full border rounded p-2">
        </div>

        @if(isset($groups) && $groups->count() > 0)
            <div>
                <label class="block font-medium mb-2">Доступен группам (можно задать своё время открытия/закрытия для каждой группы):</label>
                <div class="space-y-4">
                    @foreach($groups as $group)
                        <div class="border rounded p-4 hover:bg-gray-50">
                            <label class="flex items-center space-x-2 mb-3">
                                <input type="checkbox" name="groups[]" value="{{ $group->id }}"
                                       class="group-checkbox text-blue-600 focus:ring-blue-500">
                                <span class="font-medium">{{ $group->name }}</span>
                            </label>
                            <div class="ml-6 grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <label class="block text-gray-600 mb-1">Открыть с:</label>
                                    <input type="datetime-local" name="group_period_start[{{ $group->id }}]"
                                           class="w-full border rounded p-2 text-sm"
                                           placeholder="Или общие даты курса">
                                </div>
                                <div>
                                    <label class="block text-gray-600 mb-1">Закрыть до:</label>
                                    <input type="datetime-local" name="group_period_end[{{ $group->id }}]"
                                           class="w-full border rounded p-2 text-sm"
                                           placeholder="Или общие даты курса">
                                </div>
                            </div>
                            <p class="ml-6 text-xs text-gray-500 mt-1">Оставьте пустым — будут использоваться общие даты курса выше</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-gray-500">Пока нет созданных групп. <a href="{{ route('admin.groups.create') }}" class="text-blue-600 underline">Создать группу</a></p>
        @endif

        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Сохранить
        </button>
    </form>
</div>

</body>
</html>
