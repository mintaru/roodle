<h1>Группа: {{ $group->name }}</h1>

<h2>Изменить название группы</h2>
<form action="{{ route('admin.groups.update', $group) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="text" name="name" value="{{ $group->name }}" placeholder="Название группы" required>
    <button type="submit">Сохранить</button>
</form>

<hr>

<h2>Добавить студента</h2>
<form action="{{ route('admin.groups.assign', $group) }}" method="POST">
    @csrf
    <select name="user_id">
        @foreach($students as $student)
            <option value="{{ $student->id }}">{{ $student->name }}</option>
        @endforeach
    </select>
    <button type="submit">Добавить</button>
</form>

<hr>

<h2>Список студентов группы</h2>
<ul>
@foreach($group->users as $user)
    <li>
        {{ $user->name }}
        <form action="{{ route('admin.groups.destroy', [$group, $user]) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit">Удалить</button>
        </form>
    </li>
@endforeach
</ul>
