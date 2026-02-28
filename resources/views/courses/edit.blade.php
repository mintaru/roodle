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

        <div>
            <label class="block font-medium">Доступен с:</label>
            <input type="datetime-local" name="period_start" value="{{ old('period_start', $course->formatPeriodForInput('period_start')) }}"
                   class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block font-medium">Доступен до:</label>
            <input type="datetime-local" name="period_end" value="{{ old('period_end', $course->formatPeriodForInput('period_end')) }}"
                   class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block font-medium mb-2">Доступен группам (можно задать своё время открытия/закрытия для каждой группы):</label>
            <div class="space-y-4">
                @foreach($groups as $group)
                    @php
                        $pivot = $course->groups->firstWhere('id', $group->id)?->pivot;
                    @endphp
                    <div class="border rounded p-4 hover:bg-gray-50">
                        <label class="flex items-center space-x-2 mb-3">
                            <input type="checkbox" name="groups[]" value="{{ $group->id }}"
                                   class="group-checkbox text-blue-600 focus:ring-blue-500"
                                   @if($course->groups->contains($group->id)) checked @endif>
                            <span class="font-medium">{{ $group->name }}</span>
                        </label>
                        <div class="ml-6 grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <label class="block text-gray-600 mb-1">Открыть с:</label>
                                <input type="datetime-local" name="group_period_start[{{ $group->id }}]"
                                       value="{{ $pivot && $pivot->period_start ? \Carbon\Carbon::parse($pivot->period_start, 'UTC')->setTimezone('Asia/Krasnoyarsk')->format('Y-m-d\TH:i') : '' }}"
                                       class="w-full border rounded p-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-gray-600 mb-1">Закрыть до:</label>
                                <input type="datetime-local" name="group_period_end[{{ $group->id }}]"
                                       value="{{ $pivot && $pivot->period_end ? \Carbon\Carbon::parse($pivot->period_end, 'UTC')->setTimezone('Asia/Krasnoyarsk')->format('Y-m-d\TH:i') : '' }}"
                                       class="w-full border rounded p-2 text-sm">
                            </div>
                        </div>
                        <p class="ml-6 text-xs text-gray-500 mt-1">Оставьте пустым — будут использоваться общие даты курса выше</p>
                    </div>
                @endforeach
            </div>
        </div>


        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Обновить
        </button>
    </form>
</div>

</body>
</html>
