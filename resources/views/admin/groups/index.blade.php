<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Группы студентов</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
</head>

<body>
    @include('components.menu')

    <style>
        .admin-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem;
        }

        .admin-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .admin-card h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
        }

        .success-message {
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            color: #2e7d32;
            font-size: 14px;
        }

        .btn-primary {
            display: inline-block;
            margin-bottom: 1.5rem;
            padding: 10px 18px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }

        .search-box {
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .search-box form {
            display: grid;
            grid-template-columns: 1fr 1fr auto auto;
            gap: 1rem;
            align-items: flex-end;
        }

        .search-box form > div {
            flex: 1;
        }

        .search-box label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .search-box select,
        .search-box input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Manrope', sans-serif;
        }

        .search-box select:focus,
        .search-box input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-search,
        .btn-reset {
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            font-family: 'Manrope', sans-serif;
            transition: all 0.2s ease;
        }

        .btn-search {
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-reset {
            background: #e0e0e0;
            color: #333;
        }

        .btn-reset:hover {
            background: #d0d0d0;
        }

        .groups-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .groups-table thead {
            background: #f5f5f5;
            border-bottom: 2px solid #e0e0e0;
        }

        .groups-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
        }

        .groups-table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }

        .groups-table tbody tr:hover {
            background: #fafafa;
        }

        .table-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .table-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .btn-danger {
            color: #e74c3c;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            font-family: 'Manrope', sans-serif;
        }

        .btn-danger:hover {
            text-decoration: underline;
        }

        @media (max-width: 1024px) {
            .search-box form {
                grid-template-columns: 1fr;
            }

            .admin-container {
                padding: 1rem;
            }

            .admin-card {
                padding: 1rem;
            }
        }
    </style>

    <div class="admin-container">
        <div class="admin-card">

            <h1>Группы студентов</h1>

            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            <a href="{{ route('admin.groups.create') }}" class="btn btn-primary">
                Создать новую группу
            </a>

            <!-- Search Form -->
            <div class="search-box">
                <form method="GET" action="{{ route('admin.groups.index') }}">
                    <div>
                        <label>Искать по колонке:</label>
                        <select name="search_column" id="search_column">
                            <option value="name" {{ $searchColumn === 'name' ? 'selected' : '' }}>Название группы</option>
                            <option value="id" {{ $searchColumn === 'id' ? 'selected' : '' }}>ID</option>
                            <option value="users_count" {{ $searchColumn === 'users_count' ? 'selected' : '' }}>Количество студентов</option>
                        </select>
                    </div>
                    <div>
                        <label>Поисковый запрос:</label>
                        <input type="text" name="search_value" placeholder="Введите текст для поиска..." value="{{ $searchValue }}">
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-bottom:0">Поиск</button>
                    <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary">Очистить</a>
                </form>
            </div>

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
                    @foreach($groups as $group)
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
                                    <button type="submit" class="btn-danger" onclick="return confirm('Вы уверены, что хотите удалить эту группу?')">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
