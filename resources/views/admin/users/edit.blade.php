<h1>Редактирование пользователя: {{ $user->name }}</h1>

<form action="{{ route('admin.users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')

    <div>
        <label>ФИО:</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
    </div>

    <div>
        <label>Логин (username):</label>
        <input type="text" name="username" value="{{ old('username', $user->username) }}" required>
    </div>

    <div>
        <label>Новый пароль (если нужно):</label>
        <input type="password" name="password" placeholder="Оставьте пустым, если не меняете">
    </div>

    <div>
        <label>Роль:</label>
        <select name="role" required>
            @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Группы:</label><br>
        @foreach($groups as $group)
            <label>
                <input type="checkbox" name="groups[]" value="{{ $group->id }}"
                    {{ $user->groups->contains($group->id) ? 'checked' : '' }}>
                {{ $group->name }}
            </label><br>
        @endforeach
    </div>

    <div>
        <label>Права:</label><br>
        @foreach($permissions as $permission)
            <label>
                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                    {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                {{ $permission->name }}
            </label><br>
        @endforeach
    </div>

    <button type="submit">Сохранить</button>
</form>

<a href="{{ route('admin.users.index') }}">Назад</a>