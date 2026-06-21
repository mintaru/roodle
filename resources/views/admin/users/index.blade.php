<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Пользователи</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
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
        <div class="admin-card" x-data="{ importModalOpen: false, importMode: 'auto', groupMode: 'none' }">

            <h1>Пользователи</h1>

            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    Создать нового пользователя
                </a>
                <button type="button" @click="importModalOpen = true"
                    class="btn btn-primary" style="background:linear-gradient(135deg,#43a047,#2e7d32);">
                    Импорт студентов из Excel
                </button>
            </div>

            @livewire('admin.user-search')

            {{-- Модалка импорта из Excel --}}
            <template x-teleport="body">
                <div x-show="importModalOpen" x-cloak class="modal-backdrop" @click.self="importModalOpen = false">
                    <div class="modal-box" style="max-width:620px;">
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:1.25rem;">
                            <div class="modal-icon" style="background:#e8f5e9;">
                                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#2e7d32" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 style="margin:0; font-size:18px; font-weight:700; color:#1e2530;">Импорт студентов из Excel</h3>
                                <p style="margin:2px 0 0; font-size:13px; color:#6b7a89;">Загрузите файл .xlsx, .xls или .csv</p>
                            </div>
                        </div>

                        <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- Файл --}}
                            <div style="margin-bottom:1rem;">
                                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Файл Excel</label>
                                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                                    style="width:100%; padding:8px 10px; border:1px solid #ccc; border-radius:6px; font-size:14px;">
                            </div>

                            {{-- Пропустить первую строку --}}
                            <div style="margin-bottom:1rem; display:flex; align-items:center; gap:8px;">
                                <input type="checkbox" name="skip_first_row" value="1" checked id="skip_first_row">
                                <label for="skip_first_row" style="font-size:13px; color:#333; cursor:pointer;">Первая строка — заголовки (пропустить при импорте)</label>
                            </div>

                            {{-- Пароль по умолчанию --}}
                            <div style="margin-bottom:1rem;">
                                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Пароль по умолчанию</label>
                                <input type="text" name="default_password" value="password123"
                                    style="width:100%; padding:8px 10px; border:1px solid #ccc; border-radius:6px; font-size:14px;">
                                <p style="font-size:11px; color:#999; margin:4px 0 0;">Будет использован, если в файле нет колонки с паролем</p>
                            </div>

                            {{-- Режим определения колонок --}}
                            <div style="margin-bottom:1rem;">
                                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Режим определения колонок</label>
                                <div style="display:flex; gap:12px;">
                                    <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer;">
                                        <input type="radio" name="mode" value="auto" checked x-model="importMode">
                                        Авто (по заголовкам)
                                    </label>
                                    <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer;">
                                        <input type="radio" name="mode" value="manual" x-model="importMode">
                                        Вручную (по номеру колонки)
                                    </label>
                                </div>
                            </div>

                            {{-- Ручное сопоставление колонок --}}
                            <div x-show="importMode === 'manual'" x-cloak style="margin-bottom:1rem; padding:1rem; background:#f9fafb; border-radius:8px; border:1px solid #e5e7eb;">
                                <p style="font-size:12px; color:#666; margin:0 0 10px;">Укажите номер колонки (1, 2, 3...) для каждого поля:</p>
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                    <div>
                                        <label style="display:block; font-size:12px; font-weight:600; color:#333; margin-bottom:4px;">Колонка с именем *</label>
                                        <input type="number" name="col_name" value="1" min="1" style="width:100%; padding:6px 8px; border:1px solid #ccc; border-radius:4px; font-size:13px;">
                                    </div>
                                    <div>
                                        <label style="display:block; font-size:12px; font-weight:600; color:#333; margin-bottom:4px;">Колонка с логином</label>
                                        <input type="number" name="col_username" min="1" placeholder="напр. 2" style="width:100%; padding:6px 8px; border:1px solid #ccc; border-radius:4px; font-size:13px;">
                                    </div>
                                    <div>
                                        <label style="display:block; font-size:12px; font-weight:600; color:#333; margin-bottom:4px;">Колонка с паролем</label>
                                        <input type="number" name="col_password" min="1" placeholder="напр. 3" style="width:100%; padding:6px 8px; border:1px solid #ccc; border-radius:4px; font-size:13px;">
                                    </div>
                                    <div>
                                        <label style="display:block; font-size:12px; font-weight:600; color:#333; margin-bottom:4px;">Колонка с группой</label>
                                        <input type="number" name="col_group" min="1" placeholder="напр. 4" style="width:100%; padding:6px 8px; border:1px solid #ccc; border-radius:4px; font-size:13px;">
                                    </div>
                                </div>
                                <p style="font-size:11px; color:#999; margin:8px 0 0;">Оставьте поле пустым, если колонка отсутствует</p>
                            </div>

                            {{-- Назначение группы --}}
                            <div style="margin-bottom:1rem;">
                                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Назначение группы</label>
                                <div style="display:flex; flex-direction:column; gap:8px;">
                                    <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer;">
                                        <input type="radio" name="group_mode" value="none" checked x-model="groupMode">
                                        Не назначать группу
                                    </label>
                                    <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer;">
                                        <input type="radio" name="group_mode" value="select" x-model="groupMode">
                                        Выбрать группу для всех
                                    </label>
                                    <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer;">
                                        <input type="radio" name="group_mode" value="column" x-model="groupMode">
                                        Взять из колонки файла
                                    </label>
                                </div>

                                <div x-show="groupMode === 'select'" x-cloak style="margin-top:8px;">
                                    <select name="assign_group" x-bind:disabled="groupMode !== 'select'" style="width:100%; padding:8px 10px; border:1px solid #ccc; border-radius:6px; font-size:14px;">
                                        <option value="">Выберите группу</option>
                                        @foreach(\App\Models\Group::orderBy('name')->get() as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Кнопки --}}
                            <div class="modal-actions">
                                <button type="button" class="modal-btn modal-btn--cancel" @click="importModalOpen = false">Отмена</button>
                                <button type="submit" class="modal-btn" style="flex:1; padding:11px; border-radius:999px; font-size:14px; font-weight:700; font-family:'Manrope',sans-serif; border:none; cursor:pointer; transition:.2s; background:linear-gradient(135deg,#43a047,#2e7d32); color:#fff;">
                                    Импортировать
                                </button>
                            </div>
                        </form>

                        {{-- Инструкция --}}
                        <div style="margin-top:1.25rem; padding:1rem; background:#f0f7ff; border-radius:8px; border:1px solid #d0e3f7;">
                            <h4 style="margin:0 0 8px; font-size:13px; font-weight:700; color:#1a4978;">Требования к Excel-файлу</h4>
                            <ul style="margin:0; padding-left:18px; font-size:12px; color:#333; line-height:1.7;">
                                <li><strong>Формат:</strong> .xlsx, .xls или .csv</li>
                                <li><strong>Первая строка</strong> должна содержать заголовки колонок (для авто-режима)</li>
                                <li><strong>Авто-режим</strong> распознаёт колонки по заголовкам:
                                    <code style="background:#e0e8f0; padding:1px 5px; border-radius:3px;">Имя</code>,
                                    <code style="background:#e0e8f0; padding:1px 5px; border-radius:3px;">Логин</code>,
                                    <code style="background:#e0e8f0; padding:1px 5px; border-radius:3px;">Пароль</code>,
                                    <code style="background:#e0e8f0; padding:1px 5px; border-radius:3px;">Группа</code>
                                </li>
                                <li><strong>Обязательно:</strong> минимум колонка с именем студента</li>
                                <li><strong>Логин</strong> — если не указан, генерируется автоматически из имени</li>
                                <li><strong>Пароль</strong> — если не указан, используется пароль по умолчанию</li>
                                <li><strong>Группа</strong> — если колонка есть, группа создаётся автоматически (или выберите вручную)</li>
                                <li><strong>Дубликаты</strong> по логину пропускаются</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </template>

        </div>
    </div>

</body>
</html>
