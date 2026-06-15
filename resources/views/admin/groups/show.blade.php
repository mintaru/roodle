<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Группа: {{ $group->name }}</title>
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

        .form-card + .form-card {
            margin-top: 1.5rem;
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

        .form-divider {
            height: 1px;
            background: var(--color-border);
            margin: 1.75rem 0;
        }

        .form-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--color-border);
        }

        .student-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .student-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 14px;
            border: 1px solid var(--color-border);
            border-radius: var(--r-md);
            background: var(--color-surface-2);
            font-size: 14px;
            color: var(--color-text-primary);
            transition: var(--transition);
        }

        .student-item:hover {
            border-color: var(--color-border-2);
        }

        .empty-state {
            font-size: 14px;
            color: var(--color-text-muted);
            text-align: center;
            padding: 2rem;
        }
    </style>
</head>
<body>

    @include('components.menu')

    <div class="layout">

        {{-- Sidebar --}}
        <aside class="sidebar">

            <p class="sidebar-section-title">
                Навигация
            </p>

            <a href="{{ route('admin.groups.index') }}" class="sidebar-link">

                <svg width="16"
                     height="16"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     viewBox="0 0 24 24">

                    <path d="M19 12H5M12 5l-7 7 7 7"/>

                </svg>

                К списку групп

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

                <a href="{{ route('admin.groups.index') }}"
                   style="
                        color: var(--color-text-muted);
                        text-decoration: none;
                        transition: color 0.2s;
                   "
                   onmouseover="this.style.color='var(--teal-600)'"
                   onmouseout="this.style.color='var(--color-text-muted)'">

                    Группы

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
                    {{ $group->name }}
                </span>

            </nav>

            <div class="page-title">Группа: {{ $group->name }}</div>

            {{-- Edit group name --}}
            <div class="form-card" style="margin-top: 1.5rem;">
                <div class="form-section-label">Изменить название группы</div>

                <form action="{{ route('admin.groups.update', $group) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label for="group_name">Название группы</label>
                        <input type="text" id="group_name" name="name" value="{{ $group->name }}"
                            placeholder="Введите название группы" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M20 6L9 17l-5-5"/>
                            </svg>
                            Сохранить
                        </button>
                    </div>
                </form>
            </div>

            {{-- Add student --}}
            <div class="form-card">
                <div class="form-section-label">Добавить студента</div>

                <form action="{{ route('admin.groups.assign', $group) }}" method="POST">
                    @csrf

                    <div class="field">
                        <label for="student_id">Выберите студента</label>
                        <select id="student_id" name="user_id" required>
                            <option value="">-- Выберите студента --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            Добавить
                        </button>
                    </div>
                </form>
            </div>

            {{-- Student list --}}
            <div class="form-card">
                <div class="form-section-label">Список студентов группы</div>

                @if($group->users->count() > 0)
                    <div class="student-list" x-data="{
                        deleteUrl: '',
                        deleteName: '',
                        openDeleteModal(name, id) {
                            this.deleteName = name;
                            this.deleteUrl = '{{ url('admin/users') }}' + '/' + id;
                            this.$refs.overlay.style.display = 'flex';
                            document.body.style.overflow = 'hidden';
                        },
                        closeDeleteModal() {
                            this.$refs.overlay.style.display = 'none';
                            document.body.style.overflow = '';
                        }
                    }">
                        @foreach($group->users as $user)
                            <div class="student-item">
                                <span>{{ $user->name }}</span>

                                <button type="button" class="btn btn-ghost"
                                    @click="openDeleteModal('{{ $user->name }}', {{ $user->id }})"
                                    style="font-size: 12px; padding: 5px 10px; color: var(--red-500); border: 1px solid var(--red-200); cursor: pointer;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M18 6L6 18M6 6l12 12"/>
                                    </svg>
                                    Удалить
                                </button>
                            </div>
                        @endforeach

                        {{-- Delete confirmation modal --}}
                        <div x-ref="overlay"
                            style="display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(0,0,0,0.4); align-items: center; justify-content: center; padding: 1rem;"
                            @click="if ($event.target === $el) closeDeleteModal()">

                            <div style="background: var(--color-surface); border-radius: var(--r-lg); border: 1px solid var(--color-border); width: 100%; max-width: 400px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); overflow: hidden;"
                                @click="event.stopPropagation()">

                                <div style="padding: 1.25rem 1.5rem;">
                                    <div style="width: 40px; height: 40px; border-radius: var(--r-md); background: rgba(244, 63, 94, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                                        <svg width="18" height="18" fill="none" stroke="var(--red-500)" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M12 9v4M12 17h.01"/>
                                            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                        </svg>
                                    </div>

                                    <p style="font-size: 15px; font-weight: 700; color: var(--color-text-primary); margin: 0 0 4px;">
                                        Удалить студента
                                    </p>
                                    <p style="font-size: 13px; color: var(--color-text-secondary); margin: 0;">
                                        Вы уверены, что хотите удалить <strong x-text="deleteName"></strong> из группы?
                                    </p>
                                </div>

                                <div style="padding: 0.75rem 1.5rem; border-top: 1px solid var(--color-border); display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                                    <button type="button" class="btn btn-ghost"
                                        @click="closeDeleteModal()"
                                        style="font-size: 13px; padding: 8px 16px;">
                                        Отмена
                                    </button>
                                    <form method="POST" :action="deleteUrl" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-primary"
                                            style="font-size: 13px; padding: 8px 20px; background: var(--red-500); border-color: var(--red-500);">
                                            Удалить
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="empty-state">В группе нет студентов</div>
                @endif
            </div>

        </main>

    </div>

</body>
</html>
