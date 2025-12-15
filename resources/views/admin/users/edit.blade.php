<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование пользователя</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Редактирование пользователя: {{ $user->name }}</h1>

    @if($errors->any())
        <div class="p-3 bg-red-200 text-red-800 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">ФИО</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Введите ФИО">
        </div>

        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Логин (username)</label>
            <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Введите логин">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Новый пароль (если нужно)</label>
            <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Оставьте пустым, если не меняете">
        </div>

        <div>
            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Роль</label>
            <select id="role" name="role" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="border-t border-gray-200 pt-4">
            <label class="block text-sm font-medium text-gray-700 mb-3">Группы</label>
            <div class="space-y-2">
                @foreach($groups as $group)
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="groups[]" value="{{ $group->id }}"
                            {{ $user->groups->contains($group->id) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <span class="ml-2 text-gray-700">{{ $group->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="border-t border-gray-200 pt-4">
            <label class="block text-sm font-medium text-gray-700 mb-3">Права</label>
            <div class="space-y-2">
                @foreach($permissions as $permission)
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                            {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <span class="ml-2 text-gray-700">{{ $permission->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3 pt-6 border-t border-gray-200">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Сохранить изменения</button>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500 inline-block">Отмена</a>
        </div>
    </form>
</div>

</body>
</html>