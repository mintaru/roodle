<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание пользователя</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-text-secondary);
            text-decoration: none;
            padding: 6px 12px;
            border-radius: var(--r-sm);
            border: 1px solid var(--color-border);
            background: var(--color-surface);
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }
        .back-link:hover {
            color: var(--color-text-primary);
            background: var(--color-surface-2);
        }
        .page-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .page-subtitle {
            font-size: 14px;
            color: var(--color-text-secondary);
            margin-bottom: 2rem;
        }
        .form-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--r-xl);
            padding: 2rem;
            max-width: 560px;
            box-shadow: var(--shadow-sm);
        }
        .form-section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--color-text-muted);
            margin-bottom: 1rem;
        }
        .field {
            margin-bottom: 1.25rem;
        }
        .field:last-child { margin-bottom: 0; }
        .field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-text-secondary);
            margin-bottom: 6px;
        }
        .field input[type=text],
        .field input[type=password],
        .field select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--color-border-2);
            border-radius: var(--r-sm);
            font-size: 14px;
            font-family: var(--font-body);
            color: var(--color-text-primary);
            background: var(--color-surface);
            transition: var(--transition);
            appearance: none;
        }
        .field input:focus,
        .field select:focus {
            outline: none;
            border-color: var(--teal-400);
            box-shadow: 0 0 0 3px rgba(0, 181, 165, .1);
        }
        .field select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7a89' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
        }
        .field-hint {
            font-size: 12px;
            color: var(--color-text-muted);
            margin-top: 4px;
        }
        .password-wrapper { position: relative; }
        .password-wrapper input { padding-right: 44px; }
        .pw-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--color-text-muted);
            padding: 0;
            display: flex;
            align-items: center;
            transition: var(--transition);
        }
        .pw-toggle:hover { color: var(--color-text-secondary); }
        .form-divider {
            height: 1px;
            background: var(--color-border);
            margin: 1.75rem 0;
        }
        .checkbox-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            border: 1px solid var(--color-border);
            border-radius: var(--r-sm);
            cursor: pointer;
            transition: var(--transition);
            font-size: 13px;
            font-weight: 500;
            color: var(--color-text-secondary);
            background: var(--color-surface-2);
        }
        .checkbox-item:hover {
            border-color: var(--color-border-2);
            color: var(--color-text-primary);
        }
        .checkbox-item input[type=checkbox] {
            width: 15px;
            height: 15px;
            accent-color: var(--teal-500);
            flex-shrink: 0;
            cursor: pointer;
        }
        .form-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--color-border);
        }
    </style>
</head>
<body>

@include('components.menu')

<div class="main" style="max-width: 720px; margin: 2rem auto; padding: 0 1.5rem;">

    <x-back-button :url="route('admin.users.index')" text="К списку пользователей" />

    <div class="page-title">Создание пользователя</div>
    <div class="page-subtitle">Заполните данные нового участника системы</div>

    <div class="form-card">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            {{-- Личные данные --}}
            <div class="form-section-label">Личные данные</div>

            <div class="field">
                <label for="name">ФИО</label>
                <input type="text" id="name" name="name"
                       value="{{ old('name') }}"
                       placeholder="Иванов Иван Иванович"
                       required>
                @error('name')
                    <div class="field-hint" style="color: var(--red-500)">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="username">Логин</label>
                <input type="text" id="username" name="username"
                       value="{{ old('username') }}"
                       placeholder="ivanov_ivan"
                       required>
                <div class="field-hint">Используется для входа в систему</div>
                @error('username')
                    <div class="field-hint" style="color: var(--red-500)">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">Пароль</label>
                <div class="password-wrapper" x-data="{ show: false }">
                    <input :type="show ? 'text' : 'password'"
                           id="password" name="password"
                           placeholder="Минимум 8 символов"
                           required>
                    <button type="button" class="pw-toggle" @click="show = !show">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <template x-if="!show">
                                <g><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></g>
                            </template>
                            <template x-if="show">
                                <g><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></g>
                            </template>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <div class="field-hint" style="color: var(--red-500)">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-divider"></div>

            {{-- Доступ --}}
            <div class="form-section-label">Доступ</div>

            <div class="field">
                <label for="role">Роль</label>
                <select id="role" name="role" required>
                    <option value="" disabled selected>Выберите роль...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}"
                            {{ old('role') === $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <div class="field-hint" style="color: var(--red-500)">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-divider"></div>

            {{-- Группы --}}
            <div class="form-section-label">Группы</div>

            <div class="checkbox-grid">
                @foreach($groups as $group)
                    <label class="checkbox-item">
                        <input type="checkbox"
                               name="groups[]"
                               value="{{ $group->id }}"
                               {{ in_array($group->id, old('groups', [])) ? 'checked' : '' }}>
                        {{ $group->name }}
                    </label>
                @endforeach
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M20 6L9 17l-5-5"/>
                    </svg>
                    Создать пользователя
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Отмена</a>
            </div>

        </form>
    </div>

</div>

</body>
</html>
