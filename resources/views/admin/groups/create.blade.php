<h1>Создать новую группу</h1>

@if ($errors->any())
    <div style="color:red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.groups.store') }}" method="POST">
    @csrf
    <div>
        <label>Название группы:</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
    </div>
    <button type="submit">Создать группу</button>
</form>

<a href="{{ route('admin.groups.index') }}">Назад к списку групп</a>