
<h1>Пользователи</h1>

<a href="{{ route('admin.users.create') }}">Создать нового пользователя</a>

<table border="1" cellpadding="8">
    <tr>
        <th>Имя</th>
        <th>Роль</th>
        <th>Группы</th>
        <th>Действия</th>
    </tr>
    @foreach($users as $user)
    <tr>
        <td>{{ $user->name }}</td>
        <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
        <td>{{ $user->groups->pluck('name')->join(', ') }}</td>
        <td>
            <a href="{{ route('admin.users.edit', $user) }}">Редактировать</a>
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit">Удалить</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
