<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результаты теста: {{ $test->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 16px;
            background: #ccc;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: background 0.3s;
        }

        .back-button:hover {
            background: #aaa;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th {
            background: #2c3e50;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 14px;
            font-weight: 600;
            border-bottom: 2px solid #1a252f;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            min-width: 80px;
        }

        .status-in-progress {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-not-started {
            background: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }

        .score-cell {
            font-weight: 600;
            text-align: center;
        }

        .score-high {
            color: #28a745;
        }

        .score-medium {
            color: #ffc107;
        }

        .score-low {
            color: #dc3545;
        }

        .results-section {
            background: white;
            padding: 10px;
            border-radius: 4px;
            margin-top: 8px;
            font-size: 12px;
            color: #666;
        }

        .results-section strong {
            display: block;
            margin-bottom: 4px;
            color: #333;
        }

        .result-item {
            margin: 4px 0;
            padding: 4px;
            background: #f9f9f9;
            border-left: 3px solid #ddd;
            padding-left: 8px;
        }

        .result-item.correct {
            border-left-color: #28a745;
            color: #155724;
        }

        .result-item.incorrect {
            border-left-color: #dc3545;
            color: #721c24;
        }

        .empty-message {
            grid-column: 1 / -1;
            padding: 40px;
            text-align: center;
            color: #999;
        }

        .filter-section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-section label {
            display: inline-block;
            margin-right: 15px;
            font-size: 14px;
        }

        .filter-section input[type="checkbox"] {
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            table {
                font-size: 12px;
            }

            th, td {
                padding: 8px;
            }

            .status-badge {
                min-width: 70px;
                font-size: 11px;
            }

            .results-section {
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
@include('components.menu')

<div class="container">
    <a href="{{ route('courses.show', $course) }}" class="back-button">← Назад к курсу</a>

    <div class="header">
        <h1>Результаты теста: {{ $test->title }}</h1>
        <p>Курс: <strong>{{ $course->title }}</strong></p>
        <p>Всего студентов в курсе: <strong>{{ count($studentsData) }}</strong></p>
    </div>

    <div class="filter-section">
        <label>
            <input type="checkbox" id="filter-in-progress" checked>
            В процессе
        </label>
        <label>
            <input type="checkbox" id="filter-completed" checked>
            Завершили
        </label>
        <label>
            <input type="checkbox" id="filter-not-started" checked>
            Не начинали
        </label>
    </div>

    @if(count($studentsData) > 0)
        <table id="results-table">
            <thead>
                <tr>
                    <th style="width: 20%">Студент</th>
                    <th style="width: 15%">Статус</th>
                    <th style="width: 10%">Попытка</th>
                    <th style="width: 10%">Минут на тесте</th>
                    <th style="width: 15%">Результат текущей</th>
                    <th style="width: 25%">Результаты предыдущих попыток</th>
                    <th style="width: 5%">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentsData as $data)
                    <tr class="student-row" data-status="{{ $data['status'] }}">
                        <td>
                            <strong>{{ $data['user']->name }}</strong><br>
                            <small style="color: #999;">{{ $data['user']->username }}</small>
                        </td>
                        <td>
                            @php
                                $statusClass = match($data['status']) {
                                    'в процессе' => 'status-in-progress',
                                    'завершили' => 'status-completed',
                                    default => 'status-not-started'
                                }
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $data['status'] }}</span>
                        </td>
                        <td style="text-align: center;">
                            <strong>{{ $data['current_attempt_number'] }}</strong>
                        </td>
                        <td style="text-align: center;">
                            @if($data['minutes_spent'] !== null)
                                {{ \App\Helpers\TimeFormatter::formatMinutes($data['minutes_spent']) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="score-cell">
                            @if($data['active_attempt'])
                                @if($data['active_attempt']->ended_at)
                                    <span class="score-{{ $data['active_attempt']->score >= 70 ? 'high' : ($data['active_attempt']->score >= 50 ? 'medium' : 'low') }}">
                                        {{ $data['active_attempt']->score }}%
                                    </span>
                                @else
                                    <em style="color: #999;">В процессе...</em>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($data['completed_attempts']->count() > 0)
                                <div class="results-section">
                                    <strong>Все попытки:</strong>
                                    @foreach($data['completed_attempts'] as $attempt)
                                        <div class="result-item {{ $attempt->score >= 70 ? 'correct' : 'incorrect' }}">
                                            Попытка {{ $attempt->attempt_number }}: 
                                            <strong>{{ $attempt->score }}%</strong>
                                            <br>
                                            <small>{{ $attempt->ended_at->format('d.m.Y H:i') }}</small>
                                            <br>
                                            <a href="{{ route('test-attempts.details', $attempt) }}" style="color: #0891b2; text-decoration: none; font-size: 11px; font-weight: 600;">Просмотреть детали →</a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <em style="color: #999;">Нет завершённых попыток</em>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($data['active_attempt'] && $data['active_attempt']->ended_at)
                                <a href="{{ route('test-attempts.details', $data['active_attempt']) }}" style="color: #0891b2; text-decoration: none; font-weight: 600; font-size: 13px;">
                                    Просмотр ↗
                                </a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-message">
            <p>В этом курсе пока нет студентов.</p>
        </div>
    @endif
</div>

<script>
    function updateTableFilter() {
        const filterInProgress = document.getElementById('filter-in-progress').checked;
        const filterCompleted = document.getElementById('filter-completed').checked;
        const filterNotStarted = document.getElementById('filter-not-started').checked;

        document.querySelectorAll('.student-row').forEach(row => {
            const status = row.dataset.status;
            let show = false;

            if (status === 'в процессе' && filterInProgress) show = true;
            if (status === 'завершили' && filterCompleted) show = true;
            if (status === 'не начинали' && filterNotStarted) show = true;

            row.style.display = show ? '' : 'none';
        });
    }

    // Обновляем фильтр при изменении checkbox
    document.getElementById('filter-in-progress').addEventListener('change', updateTableFilter);
    document.getElementById('filter-completed').addEventListener('change', updateTableFilter);
    document.getElementById('filter-not-started').addEventListener('change', updateTableFilter);

    // Обновляем таблицу каждые 10 секунд для отображения текущего времени
    setInterval(() => {
        location.reload();
    }, 10000);
</script>
</body>
</html>
