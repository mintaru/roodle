<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование группы</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <x-back-button :url="route('admin.groups.index')" text="К списку групп" />
    </div>
    <h1 class="text-3xl font-bold mb-8 text-gray-800">Группа: {{ $group->name }}</h1>

    <!-- Форма изменения названия группы -->
    <div class="bg-white p-6 rounded shadow mb-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Изменить название группы</h2>
        <form action="{{ route('admin.groups.update', $group) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="group_name" class="block text-sm font-medium text-gray-700 mb-2">Название группы</label>
                <input type="text" id="group_name" name="name" value="{{ $group->name }}" placeholder="Введите название группы" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Сохранить</button>
        </form>
    </div>

    <!-- Форма добавления студента -->
    <div class="bg-white p-6 rounded shadow mb-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Добавить студента</h2>
        <form action="{{ route('admin.groups.assign', $group) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Выберите студента</label>
                <select id="student_id" name="user_id" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Выберите студента --</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Добавить</button>
        </form>
    </div>

    <!-- Список студентов группы -->
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Список студентов группы</h2>
        @if($group->users->count() > 0)
            <div class="space-y-2">
                @foreach($group->users as $user)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded border border-gray-200">
                        <span class="text-gray-800">{{ $user->name }}</span>
                        <form action="{{ route('admin.users.destroy', [$user]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">Удалить</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">В группе нет студентов</p>
        @endif
    </div>

</div>

</body>
</html>
