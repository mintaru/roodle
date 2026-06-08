<div>
    <x-admin-search-bar :searchColumns="$this->getSearchColumns()" />

    <table class="groups-table">
        <thead>
            <tr>
                <th>Имя</th>
                <th>Логин</th>
                <th>Роль</th>
                <th>Группы</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                    <td>{{ $user->groups->pluck('name')->join(', ') }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" class="table-link">Редактировать</a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger"
                                onclick="return confirm('Удалить пользователя?')">
                                Удалить
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
