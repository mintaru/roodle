<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Пользователи</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Пользователи</h1>

    @if(session('success'))
        <div class="p-3 bg-green-200 text-green-800 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('admin.users.create') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Создать нового пользователя</a>

    <table class="w-full border">
        <thead>
        <tr class="bg-gray-200">
            <th class="p-2 border">Имя</th>
            <th class="p-2 border">Роль</th>
            <th class="p-2 border">Группы</th>
            <th class="p-2 border">Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td class="p-2 border">{{ $user->name }}</td>
                <td class="p-2 border">{{ $user->roles->pluck('name')->join(', ') }}</td>
                <td class="p-2 border">{{ $user->groups->pluck('name')->join(', ') }}</td>
                <td class="p-2 border">
                    <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:underline">Редактировать</a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Вы уверены, что хотите удалить этого пользователя?')">Удалить</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

</body>
</html>