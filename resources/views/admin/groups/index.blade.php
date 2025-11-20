<h1 class="text-2xl font-bold mb-4">Группы студентов</h1>
<a href="{{ route('groups.create') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Создать новую группу</a>

<table class="w-full border border-gray-300 bg-white rounded shadow">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-2 border">ID</th>
            <th class="p-2 border">Название группы</th>
            <th class="p-2 border">Количество студентов</th>
            <th class="p-2 border">Действия</th>
        </tr>
    </thead>
    <tbody>
    @foreach($groups as $group)
        <tr class="hover:bg-gray-50">
            <td class="p-2 border">{{ $group->id }}</td>
            <td class="p-2 border">
                <a href="{{ route('groups.show', $group) }}" class="text-blue-600 hover:underline">
                    {{ $group->name }}
                </a>
            </td>
            <td class="p-2 border">{{ $group->users_count }} студентов</td>
            <td class="p-2 border space-x-2">
                <a href="{{ route('admin.groups.edit', $group) }}" class="text-blue-600 hover:underline">Редактировать</a>
                <form action="{{ route('admin.groups.destroy', $group) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="text-red-600 hover:underline"
                        onclick="return confirm('Вы уверены, что хотите удалить эту группу?')">
                        Удалить
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
