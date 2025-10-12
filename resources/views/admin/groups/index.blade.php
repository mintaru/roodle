<h1>Группы студентов</h1>
<a href="{{ route('groups.create') }}">Создать новую группу</a>
<ul>
@foreach($groups as $group)
    <li>
        <a href="{{ route('groups.show', $group) }}">
            {{ $group->name }} ({{ $group->users_count }} студентов)
        </a>
    </li>
@endforeach
</ul>
