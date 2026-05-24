<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Редактирование пользователя</title>
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap"
        rel="stylesheet">
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
            max-width: 720px;
            box-shadow: var(--shadow-sm);
        }

        .form-section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--color-text-muted);
            margin-bottom: 1rem;
            margin-top: 1.75rem;
        }

        .form-section-label:first-child {
            margin-top: 0;
        }

        .field {
            margin-bottom: 1.25rem;
        }

        .field:last-child {
            margin-bottom: 0;
        }

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

        .field-error {
            font-size: 12px;
            color: var(--red-500);
            margin-top: 4px;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 44px;
        }

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

        .pw-toggle:hover {
            color: var(--color-text-secondary);
        }

        .form-divider {
            height: 1px;
            background: var(--color-border);
            margin: 1.75rem 0;
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
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

        .course-permissions-container {
            margin-top: 1rem;
            margin-left: 24px;
            padding: 12px;
            background: var(--color-surface-2);
            border-radius: var(--r-sm);
            border-left: 3px solid var(--teal-400);
        }

        .course-permission-item {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 1rem;
            align-items: start;
            margin-bottom: 1rem;
        }

        .course-permission-item:last-child {
            margin-bottom: 0;
        }

        .course-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--color-text-primary);
            padding-top: 8px;
        }

        .permission-checkboxes {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .permission-checkbox {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            padding: 6px 10px;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--r-sm);
            cursor: pointer;
            transition: var(--transition);
        }

        .permission-checkbox:hover {
            border-color: var(--teal-400);
            background: var(--color-surface-2);
        }

        .permission-checkbox input {
            accent-color: var(--teal-500);
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

        .error-box {
            background: rgba(244, 63, 94, 0.1);
            border: 1px solid var(--red-300);
            border-radius: var(--r-sm);
            padding: 1rem;
            margin-bottom: 1.5rem;
            color: var(--red-600);
            font-size: 13px;
        }

        .error-box ul {
            margin: 0;
            padding-left: 1.25rem;
        }

        .error-box li {
            margin-bottom: 0.25rem;
        }

        .error-box li:last-child {
            margin-bottom: 0;
        }

        [x-cloak] {
            display: none;
        }
    </style>
</head>

<body>

    @include('components.menu')

    <div class="main" style="max-width: 720px; margin: 2rem auto; padding: 0 1.5rem;">

        <x-back-button :url="route('admin.users.index')" text="К списку пользователей" />

        <div class="page-title">Редактирование пользователя</div>
        <div class="page-subtitle">{{ $user->name }}</div>

        <div class="form-card">
            @if ($errors->any())
                <div class="error-box">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.update', $user) }}" method="POST" x-data="{
                selectedRole: '{{ $user->roles->first()?->name ?? '' }}',
                isTeacher: {{ $user->hasRole('teacher') ? 'true' : 'false' }},
                isStudent: {{ $user->hasRole('student') ? 'true' : 'false' }},
                showPassword: false,
                coursePermissions: {
                    @foreach ($user->coursePermissions as $perm)
                    {{ $perm->course_id }}: {
                        checked: true,
                        can_edit: {{ $perm->can_edit ? 'true' : 'false' }},
                        can_delete: {{ $perm->can_delete ? 'true' : 'false' }},
                        can_manage_students: {{ $perm->can_manage_students ? 'true' : 'false' }}
                    }, @endforeach
                }
            }">
                @csrf
                @method('PUT')

                <div class="form-section-label">Личные данные</div>

                <div class="field">
                    <label for="name">ФИО</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                        placeholder="Иванов Иван Иванович" required>
                    @error('name')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="username">Логин</label>
                    <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}"
                        placeholder="ivanov_ivan" required>
                    @error('username')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Новый пароль</label>
                    <div class="password-wrapper">
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                            placeholder="Оставьте пустым, если не меняете">
                        <button type="button" class="pw-toggle" @click="showPassword = !showPassword">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <template x-if="!showPassword">
                                    <g>
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </g>
                                </template>
                                <template x-if="showPassword">
                                    <g>
                                        <path
                                            d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24" />
                                        <line x1="1" y1="1" x2="23" y2="23" />
                                    </g>
                                </template>
                            </svg>
                        </button>
                    </div>
                    <div class="field-hint">Оставьте пустым, если не меняете пароль</div>
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-divider"></div>

                <div class="form-section-label">Доступ</div>

                <div class="field">
                    <label for="role">Роль</label>
                    <select id="role" name="role" required
                        @change="
                selectedRole = $event.target.value;
                isTeacher = selectedRole === 'teacher';
                isStudent = selectedRole === 'student';
            ">

                        <option value="" disabled>Выберите роль...</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Курсы и права доступа для учителей -->
                <div x-show="isTeacher" x-cloak class="form-divider"></div>
                <div x-show="isTeacher" x-cloak>
                    <div class="form-section-label">Доступ к курсам</div>

                    <template x-if="isTeacher">
                        <div class="checkbox-group">
                            @foreach ($courses as $course)
                                <div class="checkbox-item"
                                    @click="$el.querySelector('input[type=checkbox]').checked = !$el.querySelector('input[type=checkbox]').checked; if ($el.querySelector('input[type=checkbox]').checked) { coursePermissions[{{ $course->id }}] = {checked: true, can_edit: true, can_delete: false, can_manage_students: false}; } else { delete coursePermissions[{{ $course->id }}]; }">
                                    <input type="checkbox" name="teacher_courses[]" value="{{ $course->id }}"
                                        @change="
                                           if ($event.target.checked) {
                                               coursePermissions[{{ $course->id }}] = {checked: true, can_edit: true, can_delete: false, can_manage_students: false};
                                           } else {
                                               delete coursePermissions[{{ $course->id }}];
                                           }
                                       "
                                        :checked="coursePermissions[{{ $course->id }}]?.checked || false">
                                    <span>{{ $course->title }}</span>
                                </div>

                                <!-- Права доступа для курса -->
                                <template x-if="coursePermissions[{{ $course->id }}]?.checked">
                                    <div class="course-permissions-container">
                                        <div class="permission-checkboxes">
                                            <label class="permission-checkbox">
                                                <input type="checkbox"
                                                    name="course_permissions[{{ $course->id }}][can_edit]"
                                                    :checked="coursePermissions[{{ $course->id }}]?.can_edit || false"
                                                    @change="coursePermissions[{{ $course->id }}].can_edit = $event.target.checked">
                                                <span>Редактирование</span>
                                            </label>
                                            <label class="permission-checkbox">
                                                <input type="checkbox"
                                                    name="course_permissions[{{ $course->id }}][can_delete]"
                                                    :checked="coursePermissions[{{ $course->id }}]?.can_delete || false"
                                                    @change="coursePermissions[{{ $course->id }}].can_delete = $event.target.checked">
                                                <span>Удаление</span>
                                            </label>
                                        </div>
                                    </div>
                                </template>
                            @endforeach
                        </div>
                    </template>
                </div>



                {{-- Группа для студента --}}
                <div x-show="isStudent" x-cloak>
                    <div class="form-divider"></div>
                    <div class="form-section-label">Группа</div>
                    <div class="checkbox-group">
                        @foreach($groups as $group)
                            <label class="checkbox-item">
                                <input type="radio"
                                       name="group_id"
                                       value="{{ $group->id }}"
                                       {{ $user->groups->contains($group->id) ? 'checked' : '' }}
                                       style="width:15px;height:15px;accent-color:var(--teal-500);flex-shrink:0;cursor:pointer;">
                                {{ $group->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M20 6L9 17l-5-5" />
                        </svg>
                        Сохранить изменения
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Отмена</a>
                </div>

            </form>
        </div>

    </div>

</body>

</html>
