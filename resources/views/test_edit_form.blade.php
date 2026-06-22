<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование теста — Roodle</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .form-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--r-xl);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            max-width: 600px;
        }

        .form-section {
            padding: 1.75rem 2rem;
            border-bottom: 1px solid var(--color-border);
        }

        .form-section:last-child {
            border-bottom: none;
        }

        .form-section__title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: var(--color-text-muted);
            margin-bottom: 1.25rem;
        }

        .form-section__title-icon {
            width: 28px;
            height: 28px;
            border-radius: var(--r-sm);
            background: var(--teal-50);
            color: var(--teal-600);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .field {
            margin-bottom: 1.25rem;
        }

        .field:last-child {
            margin-bottom: 0;
        }

        .field label {
            display: block;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 7px;
        }

        .field .hint {
            font-size: 12px;
            color: var(--color-text-muted);
            margin-top: 5px;
        }

        .input,
        .select {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--color-border);
            border-radius: var(--r-md);
            font-size: 14px;
            font-family: var(--font-body);
            color: var(--gray-800);
            background: var(--gray-50);
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            appearance: none;
            box-sizing: border-box;
        }

        .input:focus,
        .select:focus {
            outline: none;
            border-color: var(--teal-400);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(0, 181, 165, 0.12);
        }

        .input::placeholder {
            color: var(--color-text-muted);
        }

        .input[disabled] {
            background: var(--gray-100);
            color: var(--color-text-muted);
            cursor: not-allowed;
        }

        .select-wrapper {
            position: relative;
        }

        .select-wrapper::after {
            content: '';
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 6px solid var(--gray-400);
            pointer-events: none;
        }

        .select {
            cursor: pointer;
        }

        .input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .input-group .input {
            flex: 1;
        }

        .toggle-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border: 1.5px solid var(--color-border);
            border-radius: var(--r-md);
            background: var(--gray-50);
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
        }

        .toggle-row:hover {
            border-color: var(--teal-300);
            background: var(--teal-50);
        }

        .toggle-row input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--teal-500);
            cursor: pointer;
            flex-shrink: 0;
            position: relative;
            top: 1px;
        }

        .toggle-row .toggle-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-700);
            user-select: none;
        }

        .date-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-footer {
            padding: 1.5rem 2rem;
            background: var(--gray-50);
            border-top: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .form-footer-hint {
            font-size: 12.5px;
            color: var(--color-text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-footer-hint svg {
            flex-shrink: 0;
            opacity: 0.5;
        }

        @media (max-width: 600px) {
            .form-section {
                padding: 1.5rem 1.25rem;
            }

            .form-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .form-footer .btn {
                justify-content: center;
            }

            .date-grid {
                grid-template-columns: 1fr;
            }

            .form-card {
                max-width: 100%;
            }
        }
    </style>
    <script>
        if (localStorage.getItem('dark-mode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body>

<div class="layout">
    {{-- Sidebar --}}
    <aside class="sidebar">
        <p class="sidebar-section-title">Навигация</p>
        <a href="{{ route('tests.show', $test) }}" class="sidebar-link">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            К вопросам теста
        </a>
        <a href="{{ route('home') }}" class="sidebar-link">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Все курсы
        </a>

        <p class="sidebar-section-title" style="margin-top: 2rem;">Тест</p>
        <div style="padding: 0 0.75rem;">
            <p style="font-size: 13px; font-weight: 600; color: var(--gray-800); line-height: 1.4;">{{ $test->title }}</p>
        </div>
    </aside>

    {{-- Main --}}
    <main class="main">

        {{-- Breadcrumb --}}
        <nav style="display: flex; align-items: center; gap: 8px; margin-bottom: 1.75rem; font-size: 13px; color: var(--color-text-muted);">
            <a href="{{ route('home') }}" style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">Курсы</a>
            @if ($course)
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                <a href="{{ route('courses.show', $course) }}" style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">{{ $course->title }}</a>
            @endif
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <span style="color: var(--gray-600); font-weight: 500;">Редактирование теста</span>
        </nav>

        {{-- Page header --}}
        <div class="page-header">
            <h1 class="page-header__title">Редактирование теста</h1>
        </div>

        {{-- Errors --}}
        @if ($errors->any())
            <div style="
                background:#ffebee;
                border:1px solid #ffcdd2;
                border-radius:var(--r-md);
                padding:12px 16px;
                margin-bottom:1.5rem;
                max-width:600px;
            ">
                <p style="font-size:13px; font-weight:600; color:var(--red-500); margin-bottom:6px;">
                    Пожалуйста, исправьте ошибки:
                </p>
                <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:4px;">
                    @foreach ($errors->all() as $error)
                        <li style="font-size:13px; color:var(--red-500);">• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form card --}}
        <div class="form-card">
            <form action="{{ route('tests.update-settings', $test) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- SECTION 1: Basic info --}}
                <div class="form-section">
                    <div class="form-section__title">
                        <div class="form-section__title-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        Основное
                    </div>

                    @if ($course)
                        <div style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; background: var(--teal-50); border-radius: var(--r-md); margin-bottom: 1.25rem; border: 1px solid var(--teal-100);">
                            <svg width="16" height="16" fill="none" stroke="var(--teal-600)" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
                            <span style="font-size: 13px; font-weight: 600; color: var(--teal-700);">{{ $course->title }}</span>
                        </div>
                    @endif

                    <div class="field">
                        <label for="title">Название теста <span style="color: var(--red-400);">*</span></label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="input"
                            placeholder="Например: Контрольная по теме 3"
                            value="{{ old('title', $test->title) }}"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="description">Описание теста</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="input"
                            style="resize:vertical; min-height:100px;"
                        >{{ old('description', $test->description) }}</textarea>
                    </div>

                    <div class="field">
                        <label class="toggle-row" for="add_to_bank">
                            <input type="checkbox" id="add_to_bank" name="add_to_bank" value="1" {{ $test->is_global ? 'checked' : '' }}>
                            <span class="toggle-label">Добавить в общий банк тестов (видно всем преподавателям)</span>
                        </label>
                    </div>
                </div>

                {{-- SECTION 2: Attempts & time --}}
                <div class="form-section">
                    <div class="form-section__title">
                        <div class="form-section__title-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                        </div>
                        Попытки и время
                    </div>

                    <div class="field">
                        <label for="max_attempts">Максимальное количество попыток</label>
                        <div class="input-group">
                            <input
                                type="number"
                                id="max_attempts"
                                name="max_attempts"
                                class="input"
                                min="1"
                                value="{{ old('max_attempts', $test->max_attempts > 0 ? $test->max_attempts : 1) }}"
                                style="max-width: 120px;"
                                {{ $test->max_attempts === 0 ? 'disabled' : '' }}
                            >
                        </div>
                        <p class="hint">Укажите 0, чтобы разрешить неограниченное число попыток.</p>
                    </div>

                    <div class="field">
                        <label class="toggle-row" for="unlimited_attempts">
                            <input type="checkbox" id="unlimited_attempts" name="unlimited_attempts" value="1" {{ $test->max_attempts === 0 ? 'checked' : '' }}>
                            <span class="toggle-label">Неограниченное количество попыток</span>
                        </label>
                    </div>

                    <div class="field">
                        <label for="time_limit">Ограничение по времени</label>
                        <div class="input-group">
                            <input
                                type="number"
                                name="time_limit"
                                id="time_limit"
                                value="{{ old('time_limit', $test->time_limit ?? 0) }}"
                                min="0"
                                class="input"
                                style="max-width: 120px;"
                            >
                            <span style="font-size:13.5px; color: var(--gray-500); font-weight: 600;">минут</span>
                        </div>
                        <p class="hint">Введите 0, чтобы убрать ограничение по времени.</p>
                    </div>
                </div>

                {{-- SECTION 3: Display --}}
                <div class="form-section">
                    <div class="form-section__title">
                        <div class="form-section__title-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <path d="M3 9h18M9 21V9"/>
                            </svg>
                        </div>
                        Отображение вопросов
                    </div>

                    <div class="field">
                        <label for="display_mode">Режим отображения</label>
                        <div class="select-wrapper">
                            <select id="display_mode" name="display_mode" class="select input">
                                <option value="single_page" {{ $test->display_mode === 'single_page' ? 'selected' : '' }}>Каждый вопрос на новой странице</option>
                                <option value="paged" {{ $test->display_mode === 'paged' ? 'selected' : '' }}>Выбрать расположение вопросов самому</option>
                            </select>
                        </div>
                    </div>

                    <div class="field" id="randomize_questions_block">
                        <label class="toggle-row" for="randomize_questions">
                            <input type="checkbox" id="randomize_questions" name="randomize_questions" value="1" {{ $test->randomize_questions ? 'checked' : '' }}>
                            <span class="toggle-label">Случайно перемешивать вопросы для студента</span>
                        </label>
                    </div>

                    <div class="field">
                        <label class="toggle-row" for="is_details_available">
                            <input type="checkbox" id="is_details_available" name="is_details_available" value="1" {{ $test->is_details_available ? 'checked' : '' }}>
                            <span class="toggle-label">Показывать обзор в конце</span>
                        </label>
                    </div>
                </div>

                {{-- SECTION 4: Access period --}}
                <div class="form-section">
                    <div class="form-section__title">
                        <div class="form-section__title-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2"/>
                                <path d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>
                        </div>
                        Период доступа
                    </div>

                    <div class="date-grid">
                        <div class="field" style="margin-bottom:0;">
                            <label for="period_start">Доступен с</label>
                            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                <input
                                    type="datetime-local"
                                    id="period_start"
                                    name="period_start"
                                    value="{{ old('period_start', $test->period_start ? $test->period_start->format('Y-m-d\TH:i') : '') }}"
                                    class="input"
                                    style="flex: 1; min-width: 180px;"
                                >
                                <button type="button" onclick="setToday(document.getElementById('period_start'))"
                                    style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border:1px solid var(--color-border);border-radius:var(--r-md);background:var(--color-surface);font-size:12px;color:var(--teal-600);cursor:pointer;font-family:var(--font-body);white-space:nowrap;transition:border-color 0.2s,background 0.2s,color 0.2s;"
                                    onmouseover="this.style.borderColor='var(--teal-400)';this.style.background='var(--teal-50)';this.style.color='var(--teal-700)'"
                                    onmouseout="this.style.borderColor='var(--color-border)';this.style.background='var(--color-surface)';this.style.color='var(--teal-600)'">Сегодня</button>
                            </div>
                        </div>
                        <div class="field" style="margin-bottom:0;">
                            <label for="period_end">Доступен до</label>
                            <input
                                type="datetime-local"
                                id="period_end"
                                name="period_end"
                                value="{{ old('period_end', $test->period_end ? $test->period_end->format('Y-m-d\TH:i') : '') }}"
                                class="input"
                            >
                            <p class="hint">Оставьте пустым — без ограничения.</p>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="form-footer">

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" name="action" value="view" class="btn">
                            Сохранить и перейти к тесту
                        </button>
                        <button type="submit" name="action" value="questions" class="btn btn-primary">
                            Сохранить и редактировать вопросы
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </main>
</div>

<script>
    function setToday(el) {
        var d = new Date();
        el.value = d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0') + 'T' + String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0');
    }

    const unlimitedCheckbox = document.getElementById('unlimited_attempts');
    const maxAttemptsInput = document.getElementById('max_attempts');

    unlimitedCheckbox.addEventListener('change', function () {
        if (this.checked) {
            maxAttemptsInput.disabled = true;
            maxAttemptsInput.value = 0;
        } else {
            maxAttemptsInput.disabled = false;
            maxAttemptsInput.value = 1;
        }
    });

    const displayMode = document.getElementById('display_mode');
    const randomizeBlock = document.getElementById('randomize_questions_block');
    const randomizeCheckbox = document.getElementById('randomize_questions');

    function toggleRandomize() {
        if (displayMode.value === 'paged') {
            randomizeBlock.style.display = 'none';
            randomizeCheckbox.checked = false;
        } else {
            randomizeBlock.style.display = '';
        }
    }

    displayMode.addEventListener('change', toggleRandomize);
    toggleRandomize();
</script>

</body>
</html>
