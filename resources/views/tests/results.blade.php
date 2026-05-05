<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результаты теста: {{ $test->title }}</title>

    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
    </style>
</head>

<body>

@include('components.menu')

<div class="layout">
    {{-- Sidebar --}}
    <aside class="sidebar">
        <p class="sidebar-section-title">Навигация</p>
        <a href="{{ route('courses.show', $course) }}" class="sidebar-link">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            К курсу
        </a>
        <a href="{{ route('home') }}" class="sidebar-link">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Все курсы
        </a>

        <p class="sidebar-section-title" style="margin-top: 2rem;">Курс</p>
        <div style="padding: 0 0.75rem;">
            <p style="font-size: 13px; font-weight: 600; color: var(--gray-800); line-height: 1.4;">{{ $course->title }}</p>
        </div>
    </aside>


    {{-- Main --}}
    <main class="main">

        {{-- Breadcrumb --}}
        <nav style="display: flex; align-items: center; gap: 8px; margin-bottom: 1.75rem; font-size: 13px; color: var(--color-text-muted);">
            <a href="{{ route('home') }}" style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">Курсы</a>
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <a href="{{ route('courses.show', $course) }}" style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">{{ $course->title }}</a>
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
                            <th>Время</th>
                            <th>Результат</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($studentsData as $data)
                            <tr>
                                <td>
                                    <div class="student-name">{{ $data['user']->name }}</div>
                                    <div class="student-username">{{ $data['user']->username }}</div>
                                </td>

                                <td>
                                    <span class="status-badge">
                                        {{ $data['status'] }}
                                    </span>
                                </td>

                                <td>
                                    <span class="attempt-pill">
                                        {{ $data['current_attempt_number'] ?? '—' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $data['minutes_spent'] ?? '—' }}
                                </td>

                                <td>
                                    @if($data['active_attempt'] && $data['active_attempt']->ended_at)
                                        <span class="score">
                                            {{ $data['active_attempt']->score }}%
                                        </span>
                                    @else
                                        <span class="dash">—</span>
                                    @endif
                                </td>
                            </tr>
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

</body>
</html>
