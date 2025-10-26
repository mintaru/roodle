<h1>Создание пользователя</h1>

<form action="{{ route('admin.users.store') }}" method="POST">
    @csrf

    <div>
        <label>ФИО:</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
    </div>

    <div>
        <label>Логин (username):</label>
        <input type="text" name="username" value="{{ old('username') }}" required>
    </div>

    <div>
        <label>Пароль:</label>
        <input type="password" name="password" required>
    </div>

    <div>
        <label>Роль:</label>
        <select name="role" required>
            @foreach($roles as $role)
                <option value="{{ $role->name }}">{{ $role->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Группы:</label><br>
        @foreach($groups as $group)
            <label><input type="checkbox" name="groups[]" value="{{ $group->id }}"> {{ $group->name }}</label><br>
        @endforeach
    </div>

    <button type="submit">Создать</button>
</form>