<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание пользователя</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
    <style>
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

        .course-permissions-container {
            margin-top: 1rem;
            margin-left: 24px;
            padding: 12px;
            background: var(--color-surface-2);
            border-radius: var(--r-sm);
            border-left: 3px solid var(--teal-400);
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

        [x-cloak] {
            display: none;
        }
    </style>
    <script>
        if (localStorage.getItem('dark-mode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>

<body>

    @include('components.menu')

    <div class="layout">

        {{-- Sidebar --}}
        <aside class="sidebar">

            <p class="sidebar-section-title">
                Навигация
            </p>

            <a href="{{ route('admin.users.index') }}" class="sidebar-link">

                <svg width="16"
                     height="16"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     viewBox="0 0 24 24">

                    <path d="M19 12H5M12 5l-7 7 7 7"/>

                </svg>

                К списку пользователей

            </a>

        </aside>

        {{-- Main content --}}
        <main class="main">

            {{-- Breadcrumb --}}
            <nav style="
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 1.75rem;
                font-size: 13px;
                color: var(--color-text-muted);
            ">

                <a href="{{ route('admin.users.index') }}"
                   style="
                        color: var(--color-text-muted);
                        text-decoration: none;
                        transition: color 0.2s;
                   "
                   onmouseover="this.style.color='var(--teal-600)'"
                   onmouseout="this.style.color='var(--color-text-muted)'">

                    Пользователи

                </a>

                <svg width="14"
                     height="14"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     viewBox="0 0 24 24">

                    <path d="M9 18l6-6-6-6"/>

                </svg>

                <span style="
                    color: var(--gray-600);
                    font-weight: 500;
                ">
                    Создание
                </span>

            </nav>

            <div class="page-title">Создание пользователя</div>
            <div class="page-subtitle">Заполните данные нового участника системы</div>

            <div class="form-card">
            <form action="{{ route('admin.users.store') }}" method="POST" x-data="{
                selectedRole: '{{ old('role') }}',
                isTeacher: {{ old('role') === 'teacher' ? 'true' : 'false' }},
                isStudent: {{ old('role') === 'student' ? 'true' : 'false' }},
                coursePermissions: {},
            }">
                @csrf

                {{-- Личные данные --}}
                <div class="form-section-label">Личные данные</div>

                <div class="field">
                    <label for="name">ФИО</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        placeholder="Иванов Иван Иванович" required>
                    @error('name')
                        <div class="field-hint" style="color: var(--red-500)">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="username">Логин</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                        placeholder="ivanov_ivan" required>
                    <div class="field-hint">Используется для входа в систему</div>
                    @error('username')
                        <div class="field-hint" style="color: var(--red-500)">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Пароль</label>
                    <div class="password-wrapper" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" id="password" name="password"
                            placeholder="Минимум 8 символов" required>
                        <button type="button" class="pw-toggle" @click="show = !show">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <template x-if="!show">
                                    <g>
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </g>
                                </template>
                                <template x-if="show">
                                    <g>
                                        <path
                                            d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24" />
                                        <line x1="1" y1="1" x2="23" y2="23" />
                                    </g>
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
                    <select id="role" name="role" required
                        @change="
                selectedRole = $event.target.value;
                isTeacher = selectedRole === 'teacher';
                isStudent = selectedRole === 'student';
            ">
                        <option value="" disabled selected>Выберите роль...</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="field-hint" style="color: var(--red-500)">{{ $message }}</div>
                    @enderror
                </div>
                {{-- Курсы для учителя --}}
                <div x-show="isTeacher" x-cloak>
                    <div class="form-divider"></div>
                    <div class="form-section-label">Доступ к курсам</div>

                    <div style="margin-bottom: 0.5rem;">
                        <p style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 10px;">
                            Выберите курсы и права доступа для учителя
                        </p>

                        <button type="button" onclick="openTeacherCoursesModal()"
                            style="display: inline-flex; align-items: center; gap: 8px; padding: 9px 16px; border: 1px solid var(--color-border); border-radius: var(--r-md); background: var(--color-surface); font-size: 13px; font-weight: 600; color: var(--gray-700); cursor: pointer; font-family: var(--font-body); transition: border-color 0.2s, background 0.2s;"
                            onmouseover="this.style.borderColor='var(--teal-400)'; this.style.background='var(--teal-50)'; this.style.color='var(--teal-700)'"
                            onmouseout="this.style.borderColor='var(--color-border)'; this.style.background='var(--color-surface)'; this.style.color='var(--gray-700)'">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path d="M4 19.5A2.5 2.5 0 016.5 17H20" />
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z" />
                            </svg>
                            Выбрать курсы
                            <span id="teacher-courses-badge"
                                style="display: none; background: var(--teal-500); color: #fff; font-size: 11px; font-weight: 700; border-radius: 999px; padding: 1px 7px; line-height: 18px;"></span>
                        </button>

                        <div id="teacher-courses-summary"
                            style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 6px;"></div>
                        <div id="teacher-courses-hidden-inputs"></div>
                    </div>
                </div>



                {{-- Группа для студента --}}
                <div x-show="isStudent" x-cloak>
                    <div class="form-divider"></div>
                    <div class="form-section-label">Группа</div>

                    <div style="margin-bottom: 0.5rem;">
                        <p style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 10px;">
                            Выберите группу, к которой будет привязан студент
                        </p>

                        {{-- Trigger button --}}
                        <button type="button" onclick="openStudentGroupModal()"
                            style="
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 16px;
            border: 1px solid var(--color-border);
            border-radius: var(--r-md);
            background: var(--color-surface);
            font-size: 13px; font-weight: 600;
            color: var(--gray-700);
            cursor: pointer;
            font-family: var(--font-body);
            transition: border-color 0.2s, background 0.2s;
        "
                            onmouseover="this.style.borderColor='var(--teal-400)'; this.style.background='var(--teal-50)'; this.style.color='var(--teal-700)'"
                            onmouseout="this.style.borderColor='var(--color-border)'; this.style.background='var(--color-surface)'; this.style.color='var(--gray-700)'">
                            <svg width="15" height="15" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 00-3-3.87" />
                                <path d="M16 3.13a4 4 0 010 7.75" />
                            </svg>
                            Выбрать группу
                            <span id="student-group-badge"
                                style="display: none; background: var(--teal-500); color: #fff; font-size: 11px; font-weight: 700; border-radius: 999px; padding: 1px 7px; line-height: 18px;"></span>
                        </button>

                        {{-- Chip with selected group --}}
                        <div id="student-group-summary"
                            style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 6px;"></div>

                        {{-- Hidden radio input injected by JS --}}
                        <div id="student-group-hidden-input"></div>
                    </div>
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

        </main>

    </div>

    {{-- Student group modal --}}
        <div id="student-group-modal-overlay"
            style="display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(0,0,0,0.4); align-items: center; justify-content: center; padding: 1rem;"
            onclick="handleStudentGroupOverlayClick(event)">
            <div style="background: var(--color-surface); border-radius: var(--r-lg, 12px); border: 1px solid var(--color-border); width: 100%; max-width: 460px; max-height: 80vh; display: flex; flex-direction: column; box-shadow: 0 8px 32px rgba(0,0,0,0.12); overflow: hidden;"
                onclick="event.stopPropagation()">

                <div
                    style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--color-border); display: flex; align-items: flex-start; justify-content: space-between; flex-shrink: 0;">
                    <div>
                        <p
                            style="font-size: 15px; font-weight: 700; color: var(--color-text-primary); margin: 0 0 2px;">
                            Выбор группы</p>
                        <p style="font-size: 12px; color: var(--color-text-muted); margin: 0;">Выберите одну группу для
                            студента</p>
                    </div>
                    <button type="button" onclick="closeStudentGroupModal()"
                        style="background: none; border: none; cursor: pointer; color: var(--color-text-muted); padding: 2px; margin-left: 8px; line-height: 1;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div
                    style="padding: 10px 1.25rem; border-bottom: 1px solid var(--color-border); display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" style="color: var(--color-text-muted); flex-shrink: 0;">
                        <circle cx="11" cy="11" r="8" />
                        <path d="M21 21l-4.35-4.35" />
                    </svg>
                    <input type="text" id="student-modal-group-search" placeholder="Поиск группы..."
                        oninput="filterStudentModalGroups(this.value)"
                        style="border: none; outline: none; background: transparent; font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); width: 100%; padding: 0;">
                </div>

                <div id="student-modal-group-list" style="overflow-y: auto; flex: 1; padding: 6px 0;"></div>

                <div
                    style="padding: 0.75rem 1.25rem; border-top: 1px solid var(--color-border); display: flex; align-items: center; justify-content: flex-end; gap: 8px; flex-shrink: 0;">
                    <button type="button" onclick="closeStudentGroupModal()" class="btn btn-ghost"
                        style="font-size: 13px; padding: 8px 16px;">Отмена</button>
                    <button type="button" onclick="applyStudentGroupModal()" class="btn btn-primary"
                        style="font-size: 13px; padding: 8px 20px;">Применить</button>
                </div>
                </form>
            </div>
        </div>

        <div id="teacher-courses-modal-overlay"
            style="display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(0,0,0,0.4); align-items: center; justify-content: center; padding: 1rem;"
            onclick="if(event.target===this) closeTeacherCoursesModal()">
            <div style="background: var(--color-surface); border-radius: var(--r-lg, 12px); border: 1px solid var(--color-border); width: 100%; max-width: 520px; max-height: 80vh; display: flex; flex-direction: column; box-shadow: 0 8px 32px rgba(0,0,0,0.12); overflow: hidden;"
                onclick="event.stopPropagation()">

                <div
                    style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--color-border); display: flex; align-items: flex-start; justify-content: space-between; flex-shrink: 0;">
                    <div>
                        <p
                            style="font-size: 15px; font-weight: 700; color: var(--color-text-primary); margin: 0 0 2px;">
                            Выбор курсов</p>
                        <p style="font-size: 12px; color: var(--color-text-muted); margin: 0;">Отметьте курсы и права
                            доступа</p>
                    </div>
                    <button type="button" onclick="closeTeacherCoursesModal()"
                        style="background: none; border: none; cursor: pointer; color: var(--color-text-muted); padding: 2px; margin-left: 8px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div
                    style="padding: 10px 1.25rem; border-bottom: 1px solid var(--color-border); display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" style="color: var(--color-text-muted); flex-shrink: 0;">
                        <circle cx="11" cy="11" r="8" />
                        <path d="M21 21l-4.35-4.35" />
                    </svg>
                    <input type="text" id="teacher-modal-course-search" placeholder="Поиск курса..."
                        oninput="filterTeacherModalCourses(this.value)"
                        style="border: none; outline: none; background: transparent; font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); width: 100%; padding: 0;">
                </div>

                <div id="teacher-modal-course-list" style="overflow-y: auto; flex: 1; padding: 6px 0;"></div>

                <div
                    style="padding: 0.75rem 1.25rem; border-top: 1px solid var(--color-border); display: flex; align-items: center; justify-content: flex-end; gap: 8px; flex-shrink: 0;">
                    <button type="button" onclick="closeTeacherCoursesModal()" class="btn btn-ghost"
                        style="font-size: 13px; padding: 8px 16px;">Отмена</button>
                    <button type="button" onclick="applyTeacherCoursesModal()" class="btn btn-primary"
                        style="font-size: 13px; padding: 8px 20px;">Применить</button>
                </div>
            </div>
        </div>
        <script>
            const STUDENT_GROUPS = @json($groups->map(fn($g) => ['id' => $g->id, 'name' => $g->name])->values());
            let selectedStudentGroupId = {{ old('group_id') ? old('group_id') : 'null' }};

            function openStudentGroupModal() {
                document.getElementById('student-group-modal-overlay').style.display = 'flex';
                document.getElementById('student-modal-group-search').value = '';
                renderStudentGroupList('');
                document.body.style.overflow = 'hidden';
            }

            function closeStudentGroupModal() {
                document.getElementById('student-group-modal-overlay').style.display = 'none';
                document.body.style.overflow = '';
            }

            function handleStudentGroupOverlayClick(e) {
                if (e.target === document.getElementById('student-group-modal-overlay')) {
                    closeStudentGroupModal();
                }
            }

            function filterStudentModalGroups(query) {
                renderStudentGroupList(query);
            }

            function renderStudentGroupList(query) {
                const q = query.toLowerCase().trim();
                const filtered = q ? STUDENT_GROUPS.filter(g => g.name.toLowerCase().includes(q)) : STUDENT_GROUPS;
                const list = document.getElementById('student-modal-group-list');

                if (!filtered.length) {
                    list.innerHTML =
                        '<p style="font-size: 13px; color: var(--color-text-muted); text-align: center; padding: 1.5rem;">Ничего не найдено</p>';
                    return;
                }

                list.innerHTML = filtered.map(g => {
                    const isSelected = selectedStudentGroupId == g.id;
                    return `
                <div style="padding: 0 1.25rem;">
                    <div style="
                        display: flex; align-items: center; gap: 10px;
                        padding: 9px 0;
                        border-bottom: 1px solid var(--color-border);
                        cursor: pointer;
                        background: ${isSelected ? 'var(--teal-50)' : 'transparent'};
                        margin: 0 -1.25rem;
                        padding-left: 1.25rem;
                        padding-right: 1.25rem;
                    " onclick="selectStudentGroup(${g.id})">
                        <div style="
                            width: 15px; height: 15px; border-radius: 50%;
                            border: 2px solid ${isSelected ? 'var(--teal-500)' : 'var(--color-border)'};
                            background: ${isSelected ? 'var(--teal-500)' : 'transparent'};
                            flex-shrink: 0;
                            display: flex; align-items: center; justify-content: center;
                            transition: all 0.15s;
                        ">
                            ${isSelected ? '<div style="width:5px;height:5px;border-radius:50%;background:#fff;"></div>' : ''}
                        </div>
                        <span style="font-size: 14px; flex: 1; color: var(--color-text-primary); user-select: none;">${g.name}</span>
                    </div>
                </div>
                `;
                }).join('');
            }

            function selectStudentGroup(id) {
                selectedStudentGroupId = id;
                renderStudentGroupList(document.getElementById('student-modal-group-search').value);
            }

            function applyStudentGroupModal() {
                const group = STUDENT_GROUPS.find(g => g.id == selectedStudentGroupId);

                const badge = document.getElementById('student-group-badge');
                const summary = document.getElementById('student-group-summary');
                const hiddenContainer = document.getElementById('student-group-hidden-input');

                if (group) {
                    badge.textContent = '1';
                    badge.style.display = 'inline';

                    summary.innerHTML = `
                <span style="
                    display: inline-flex; align-items: center; gap: 5px;
                    padding: 4px 10px;
                    border-radius: 999px;
                    border: 1px solid var(--teal-400);
                    background: var(--teal-50);
                    font-size: 12px;
                    color: var(--teal-700);
                ">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    ${group.name}
                </span>`;

                    hiddenContainer.innerHTML = `<input type="hidden" name="group_id" value="${group.id}">`;
                } else {
                    badge.style.display = 'none';
                    summary.innerHTML = '';
                    hiddenContainer.innerHTML = '';
                }

                closeStudentGroupModal();
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeStudentGroupModal();
            });

            const TEACHER_COURSES = @json($courses->map(fn($c) => ['id' => $c->id, 'title' => $c->title])->values());

            let teacherCoursePermissions = {};
            let tempTeacherCoursePermissions = {};

            // Восстановить состояние после old() при ошибке валидации
            @if (old('teacher_courses'))
                @foreach (old('teacher_courses') as $courseId)
                    teacherCoursePermissions[{{ $courseId }}] = {
                        checked: true,
                        can_edit: {{ old('course_permissions.' . $courseId . '.can_edit') ? 'true' : 'false' }},
                        can_delete: {{ old('course_permissions.' . $courseId . '.can_delete') ? 'true' : 'false' }}
                    };
                @endforeach
            @endif

            function openTeacherCoursesModal() {
                tempTeacherCoursePermissions = JSON.parse(JSON.stringify(teacherCoursePermissions));
                document.getElementById('teacher-courses-modal-overlay').style.display = 'flex';
                document.getElementById('teacher-modal-course-search').value = '';
                renderTeacherCourseList('');
                document.body.style.overflow = 'hidden';
            }

            function closeTeacherCoursesModal() {
                document.getElementById('teacher-courses-modal-overlay').style.display = 'none';
                document.body.style.overflow = '';
            }

            function filterTeacherModalCourses(query) {
                renderTeacherCourseList(query);
            }

            function renderTeacherCourseList(query) {
                const q = query.toLowerCase().trim();
                const filtered = q ? TEACHER_COURSES.filter(c => c.title.toLowerCase().includes(q)) : TEACHER_COURSES;
                const list = document.getElementById('teacher-modal-course-list');
                if (!filtered.length) {
                    list.innerHTML =
                        '<p style="font-size:13px;color:var(--color-text-muted);text-align:center;padding:1.5rem;">Ничего не найдено</p>';
                    return;
                }
                list.innerHTML = filtered.map(c => {
                    const p = tempTeacherCoursePermissions[c.id];
                    const isSel = p?.checked;
                    return `
        <div style="padding: 0 1.25rem; border-bottom: 1px solid var(--color-border);">
            <div style="display:flex;align-items:center;gap:10px;padding:10px 0;cursor:pointer;" onclick="toggleTeacherCourse(${c.id})">
                <div style="width:15px;height:15px;border-radius:3px;border:2px solid ${isSel ? 'var(--teal-500)' : 'var(--color-border)'};background:${isSel ? 'var(--teal-500)' : 'transparent'};flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                    ${isSel ? '<svg width="9" height="9" viewBox="0 0 12 12" fill="none" stroke="#fff" stroke-width="2.5"><path d="M2 6l3 3 5-5"/></svg>' : ''}
                </div>
                <span style="font-size:14px;flex:1;color:var(--color-text-primary);user-select:none;">${c.title}</span>
            </div>
            ${isSel ? `
                    <div style="margin-bottom:10px;margin-left:25px;display:flex;gap:8px;flex-wrap:wrap;">
                        <label style="display:inline-flex;align-items:center;gap:6px;font-size:12px;padding:5px 10px;background:var(--color-surface-2);border:1px solid var(--color-border);border-radius:var(--r-sm);cursor:pointer;">
                            <input type="checkbox" style="accent-color:var(--teal-500);cursor:pointer;"
                                ${p.can_edit ? 'checked' : ''}
                                onchange="setTeacherCoursePerm(${c.id}, 'can_edit', this.checked); event.stopPropagation();">
                            <span>Редактирование</span>
                        </label>
                        <label style="display:inline-flex;align-items:center;gap:6px;font-size:12px;padding:5px 10px;background:var(--color-surface-2);border:1px solid var(--color-border);border-radius:var(--r-sm);cursor:pointer;">
                            <input type="checkbox" style="accent-color:var(--teal-500);cursor:pointer;"
                                ${p.can_delete ? 'checked' : ''}
                                onchange="setTeacherCoursePerm(${c.id}, 'can_delete', this.checked); event.stopPropagation();">
                            <span>Удаление</span>
                        </label>
                    </div>` : ''}
        </div>`;
                }).join('');
            }

            function toggleTeacherCourse(id) {
                if (tempTeacherCoursePermissions[id]?.checked) {
                    delete tempTeacherCoursePermissions[id];
                } else {
                    tempTeacherCoursePermissions[id] = {
                        checked: true,
                        can_edit: true,
                        can_delete: false
                    };
                }
                renderTeacherCourseList(document.getElementById('teacher-modal-course-search').value);
            }

            function setTeacherCoursePerm(id, key, val) {
                if (tempTeacherCoursePermissions[id]) tempTeacherCoursePermissions[id][key] = val;
            }

            function applyTeacherCoursesModal() {
                teacherCoursePermissions = JSON.parse(JSON.stringify(tempTeacherCoursePermissions));

                const hidden = document.getElementById('teacher-courses-hidden-inputs');
                const selected = Object.entries(teacherCoursePermissions).filter(([, v]) => v.checked);

                hidden.innerHTML = selected.map(([id, p]) => `
        <input type="hidden" name="teacher_courses[]" value="${id}">
        ${p.can_edit  ? `<input type="hidden" name="course_permissions[${id}][can_edit]" value="1">` : ''}
        ${p.can_delete ? `<input type="hidden" name="course_permissions[${id}][can_delete]" value="1">` : ''}
    `).join('');

                const badge = document.getElementById('teacher-courses-badge');
                const summary = document.getElementById('teacher-courses-summary');

                if (selected.length) {
                    badge.textContent = selected.length;
                    badge.style.display = 'inline';
                    summary.innerHTML = selected.map(([id]) => {
                        const course = TEACHER_COURSES.find(c => c.id == id);
                        return `<span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:999px;border:1px solid var(--teal-400);background:var(--teal-50);font-size:12px;color:var(--teal-700);">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
                ${course?.title ?? ''}
            </span>`;
                    }).join('');
                } else {
                    badge.style.display = 'none';
                    summary.innerHTML = '';
                }

                closeTeacherCoursesModal();
            }

            // Восстановить чипы при возврате после ошибки валидации
            document.addEventListener('DOMContentLoaded', function() {
                if (Object.keys(teacherCoursePermissions).length > 0) {
                    tempTeacherCoursePermissions = JSON.parse(JSON.stringify(teacherCoursePermissions));
                    applyTeacherCoursesModal();
                }

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') {
                        closeStudentGroupModal();
                        closeTeacherCoursesModal();
                    }
                });
            });
        </script>
</body>

</html>
