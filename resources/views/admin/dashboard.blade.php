<div style="padding:20px;">
    <h1>Админ-панель</h1>
    <p>Добро пожаловать, {{ auth()->user()->name }}!</p>

    <div style="display:flex; gap:20px; margin-top:30px;">
        <a href="{{ route('admin.courses.index') }}" 
           style="padding:20px; background:#0c8d8b; color:white; text-decoration:none; border-radius:10px;">
           Список курсов
        </a>

        <a href="{{ route('groups.index') }}" 
           style="padding:20px; background:#f39c12; color:white; text-decoration:none; border-radius:10px;">
           Список групп
        </a>

        <a href="{{ route('admin.users.index') }}" 
           style="padding:20px; background:#e74c3c; color:white; text-decoration:none; border-radius:10px;">
           Список пользователей
        </a>
    </div>
</div>
