<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результаты теста: {{ $test->title }}</title>

    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <style>
        [x-cloak] { display: none !important; }

        .page-wrap {
            max-width: 1200px;
        }

        .page-title {
            font-family: var(--font-display);
            font-size: 26px;
            margin-bottom: 4px;
        }

        .page-meta {
            font-size: 13px;
            color: var(--color-text-secondary);
            margin-bottom: 1.5rem;
        }

        .stat-row {
            display: flex;
            gap: 12px;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .stat-chip {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--r-lg);
            padding: 0.75rem 1.25rem;
        }

        .filter-bar {
            display: flex;
            gap: 8px;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
        }

        .results-table-wrap {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--r-xl);
            overflow: hidden;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
        }

        .results-table th, .results-table td {
            padding: 12px;
            border-bottom: 1px solid var(--color-border);
            font-size: 13px;
        }

        .student-name { font-weight: 600; }
        .student-username { font-size: 11px; color: var(--color-text-muted); }

        .status-badge {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
        }

        .status-badge.progress { background: #fff8e1; }
        .status-badge.completed { background: #e8f5e9; }
        .status-badge.not-started { background: #f5f5f5; }

        .attempt-pill {
            background: var(--gray-100);
            padding: 5px 10px;
            border-radius: 999px;
        }

        .score { font-weight: 700; }
        .score.high { color: green; }
        .score.medium { color: orange; }
        .score.low { color: red; }

        .dash { color: var(--color-text-muted); }

        .empty-state {
            padding: 3rem;
            text-align: center;
        }

        .action-btn {
            background: var(--teal-600);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: var(--r-md);
            font-size: 12px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .action-btn:hover {
            background: var(--teal-700);
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: var(--r-xl);
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .modal-input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--color-border);
            border-radius: var(--r-md);
            font-size: 14px;
            margin-bottom: 1.5rem;
            box-sizing: border-box;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .modal-btn {
            padding: 8px 16px;
            border-radius: var(--r-md);
            border: 1px solid var(--color-border);
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-btn-primary {
            background: var(--teal-600);
            color: white;
            border: none;
        }

        .modal-btn-primary:hover {
            background: var(--teal-700);
        }

        .modal-btn-secondary:hover {
            background: var(--gray-100);
        }
    </style>
</head>

<body x-data="{ showModal: false, selectedUserId: null, selectedUserName: '', extraAttempts: '' }">

@include('components.menu')

<div class="layout">
    {{-- Sidebar --}}
    <aside class="sidebar">
        <p class="sidebar-section-title">Навигация</p>
        @if($course)
            <a href="{{ route('courses.show', $course) }}" class="sidebar-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                К курсу
            </a>
        @endif
        <a href="{{ route('home') }}" class="sidebar-link">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Все курсы
        </a>

        <p class="sidebar-section-title" style="margin-top: 2rem;">Курс</p>
        <div style="padding: 0 0.75rem;">
            @if($course)
                <p style="font-size: 13px; font-weight: 600; color: var(--gray-800); line-height: 1.4;">{{ $course->title }}</p>
            @endif
        </div>
    </aside>


    {{-- Main --}}
    <main class="main">

        {{-- Breadcrumb --}}
        <nav style="display: flex; align-items: center; gap: 8px; margin-bottom: 1.75rem; font-size: 13px; color: var(--color-text-muted);">
            <a href="{{ route('home') }}" style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">Курсы</a>
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            @if($course)
                <a href="{{ route('courses.show', $course) }}" style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">{{ $course->title }}</a>
            @endif
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <span style="color: var(--gray-600); font-weight: 500;">{{ $test->title }}</span>
        </nav>

        <div class="page-wrap">

            <h1 class="page-title">{{ $test->title }}</h1>

            @php
                $total = count($studentsData);
                $completed = collect($studentsData)->where('status','завершили')->count();
                $inProgress = collect($studentsData)->where('status','в процессе')->count();
            @endphp

            <div class="stat-row">
                <div class="stat-chip">Студентов: {{ $total }}</div>
                <div class="stat-chip">Завершили: {{ $completed }}</div>
                <div class="stat-chip">В процессе: {{ $inProgress }}</div>
            </div>

            @if($total > 0)
                <div class="results-table-wrap">
                    <table class="results-table">
                        <thead>
                        <tr>
                            <th>Студент</th>
                            <th>Статус</th>
                            <th>Попытка</th>
                            <th>Вопрос</th>
                            <th>Результат</th>
                            <th>Действия</th>
                        </tr>
                        </thead>

                        <tbody>
                            @foreach($studentsData as $data)
                            <tbody x-data="{ expanded: false }">
                                <tr>
                                    <td>
                                        <div style="display:flex; gap:10px; align-items:center;">
                                            <button type="button" @click="expanded = !expanded" style="background:transparent;border:none;cursor:pointer;font-size:16px;padding:0;margin:0;">
                                                <svg x-show="!expanded" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
                                                <svg x-show="expanded" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 9l7 7 7-7"/></svg>
                                            </button>
                                            <div>
                                                <div class="student-name">{{ $data['user']->name }}</div>
                                                <div class="student-username">{{ $data['user']->username }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="status-badge {{ strtolower(str_replace(' ', '-', $data['status'])) }}">
                                            {{ $data['status'] }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="attempt-pill">
                                            {{ $data['current_attempt_number'] ?? '—' }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($data['status'] === 'в процессе')
                                            <span class="attempt-pill">
                                                {{ $data['current_question'] ?? 1 }}/{{ $data['totalQuestions'] }}
                                            </span>
                                        @else
                                            <span class="dash">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($data['lastCompletedAttempt'])
                                            <span class="score">
                                                {{ round($data['lastCompletedAttempt']->score) }}%
                                            </span>
                                        @else
                                            <span class="dash">—</span>
                                        @endif
                                    </td>

                                    <td style="white-space:nowrap;">
                                        @can('edit courses')
                                            <button
                                                type="button"
                                                class="action-btn"
                                                @click="showModal = true; selectedUserId = {{ $data['user']->id }}; selectedUserName = '{{ $data['user']->name }}'; extraAttempts = '';"
                                            >
                                                + Попытка
                                            </button>
                                        @endcan
                                        <button type="button" class="action-btn" @click="expanded = !expanded" style="background:#4a5568;padding:6px 10px;">Просмотр попыток</button>
                                    </td>
                                </tr>

                                <tr x-show="expanded" x-cloak>
                                    <td colspan="6" style="background: #fbfcfd; padding: 12px 16px;">
                                        <div style="display:flex;flex-direction:column;gap:10px;">
                                            @if($data['attempts']->isEmpty())
                                                <div class="dash">У студента ещё нет попыток</div>
                                            @else
                                                @foreach($data['attempts'] as $attempt)
                                                    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px;border:1px solid var(--color-border);border-radius:8px;background:white;">
                                                        <div style="min-width:0;">
                                                            <div style="font-weight:600;">Попытка #{{ $attempt->attempt_number }} — {{ $attempt->ended_at ? 'Завершена' : 'В процессе' }}</div>
                                                            <div style="font-size:13px;color:var(--color-text-muted);">
                                                                {{ $attempt->started_at ? $attempt->started_at->format('d.m.Y H:i') : '—' }}
                                                                @if($attempt->ended_at)
                                                                    — {{ $attempt->ended_at->format('d.m.Y H:i') }}
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div style="display:flex;gap:8px;align-items:center;">
                                                            <div style="font-weight:700;">{{ $attempt->score !== null ? round($attempt->score) . '%' : '—' }}</div>
                                                            <a href="{{ route('test-attempts.details', $attempt) }}" class="action-btn" style="background:#2b6cb0;">Открыть</a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            @endforeach
                            </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    Нет студентов
                </div>
            @endif

        </div>

    </main>
</div>
{{-- Модалка выдачи дополнительных попыток --}}
@can('edit courses')
<div
    x-show="showModal"
    x-cloak
    class="modal-overlay"
    @click.self="showModal = false"
>
    <div class="modal-content">
        <div class="modal-title">Дополнительные попытки</div>
        <p style="font-size: 14px; color: var(--color-text-secondary); margin-bottom: 1rem;">
            Студент: <strong x-text="selectedUserName"></strong>
        </p>

        <form
            :action="`{{ route('test-attempts.grant-attempts', ['test' => $test->id, 'user' => '__USER_ID__']) }}`.replace('__USER_ID__', selectedUserId)"
            method="POST"
        >
            @csrf
            <label style="font-size: 13px; color: var(--color-text-secondary); display: block; margin-bottom: 6px;">
                Количество попыток
            </label>
            <input
                type="number"
                name="extra_attempts"
                class="modal-input"
                min="1"
                max="100"
                x-model="extraAttempts"
                placeholder="Например: 1"
                required
            >
            <div class="modal-buttons">
                <button type="button" class="modal-btn modal-btn-secondary" @click="showModal = false">
                    Отмена
                </button>
                <button type="submit" class="modal-btn modal-btn-primary">
                    Выдать
                </button>
            </div>
        </form>
    </div>
</div>
@endcan
</body>
</html>
