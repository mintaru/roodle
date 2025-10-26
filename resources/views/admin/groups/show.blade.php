<h1>Группа: {{ $group->name }}</h1>

<h2>Добавить студента</h2>
<form action="{{ route('groups.assign', $group) }}" method="POST">
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
        <form action="{{ route('groups.remove', [$group, $user]) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit">Удалить</button>
        </form>
    </li>
@endforeach
</ul>
