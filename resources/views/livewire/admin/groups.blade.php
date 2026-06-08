<div>
    <x-admin-search-bar :searchColumns="$this->getSearchColumns()" />

    <table class="groups-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название группы</th>
                <th>Количество студентов</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $group)
                <tr>
                    <td>{{ $group->id }}</td>
                    <td>
                        <a href="{{ route('admin.groups.show', $group) }}" class="table-link">
                            {{ $group->name }}
                        </a>
                    </td>
                    <td>{{ $group->users_count }}</td>
                    <td>
                        <a href="{{ route('admin.groups.show', $group) }}" class="table-link">Редактировать</a>
                        <form action="{{ route('admin.groups.destroy', $group) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger"
                                onclick="return confirm('Удалить группу?')">
                                Удалить
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
