<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание нового теста — Roodle</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <style>
        .page-wrapper {
            min-height: calc(100vh - 80px);
            background: var(--color-bg);
            padding: 2.5rem 1.5rem 4rem;
        }

        .form-container {
            max-width: 680px;
            margin: 0 auto;
        }

        /* --- Breadcrumb / back --- */
        .back-row {
            margin-bottom: 1.75rem;
        }

        /* --- Page title block --- */
        .page-title-block {
            margin-bottom: 2rem;
        }

        .page-title-block .label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--color-text-muted);
            margin-bottom: 6px;
        }

        .page-title-block h1 {
            font-family: var(--font-display);
            font-size: 30px;
            color: var(--gray-900);
            letter-spacing: -0.4px;
            line-height: 1.15;
        }

        /* --- Card --- */
        .form-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--r-2xl);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        /* --- Section dividers inside card --- */
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

        /* --- Form fields --- */
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

        /* --- Input with suffix --- */
        .input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .input-group .input {
            flex: 1;
        }

        /* --- Toggle/checkbox styled --- */
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
    align-self: center; /* ← замените margin-top на это */
    position: relative;
    top: 4px; /* ← небольшой сдвиг без влияния на layout */
}

body {
    margin:0;
    padding:0;
}

        .toggle-row .toggle-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-700);
            user-select: none;
        }

        .toggle-row .toggle-desc {
            margin-left: auto;
            font-size: 12px;
            color: var(--color-text-muted);
        }

        /* --- Date row --- */
        .date-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* --- Footer (submit) --- */
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

        /* --- Back button --- */
        .btn-back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--gray-500);
            text-decoration: none;
            padding: 6px 0;
            transition: color 0.2s;
        }

        .btn-back-link:hover {
            color: var(--gray-800);
        }

        .btn-back-link svg {
            transition: transform 0.2s;
        }

        .btn-back-link:hover svg {
            transform: translateX(-3px);
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

            .form-container {
                padding: 0;
            }
        }
    </style>
</head>
<body>


    <div class="page-wrapper">
        <div class="form-container">

            <!-- Back link -->
            <div class="back-row">
                <a href="{{ route('courses.show', $course) }}" class="btn-back-link">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 12H5M5 12l7 7M5 12l7-7"/>
                    </svg>
                    К курсу
                </a>
            </div>

            <!-- Page heading -->
            <div class="page-title-block">
                <p class="label">Новый тест</p>
                <h1>Создание теста</h1>
            </div>

            <!-- Form card -->
            <div class="form-card">
                <form action="{{ route('tests.store', $course) }}" method="POST">
                    @csrf

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

                        <div class="field">
                            <label for="title">Название теста</label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                class="input"
                                placeholder="Например: Контрольная по теме 3"
                                value="{{ old('title') }}"
                                required
                            >
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
                                    value="1"
                                    style="max-width: 120px;"
                                >
                            </div>
                            <p class="hint">Укажите 0, чтобы разрешить неограниченное число попыток.</p>
                        </div>

                        <div class="field">
                            <label class="toggle-row" for="unlimited_attempts">
                                <input type="checkbox" id="unlimited_attempts" name="unlimited_attempts" value="1">
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
                                    <option value="single_page">Каждый вопрос на новой странице</option>
                                    <option value="paged">Выбрать расположение вопросов самому</option>
                                </select>
                            </div>
                        </div>

                        <div class="field" id="randomize_questions_block">
                            <label class="toggle-row" for="randomize_questions">
                                <input type="checkbox" id="randomize_questions" name="randomize_questions" value="1">
                                <span class="toggle-label">Случайно перемешивать вопросы для студента</span>
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
                                <input
                                    type="datetime-local"
                                    id="period_start"
                                    name="period_start"
                                    value="{{ now()->format('Y-m-d\TH:i') }}"
                                    class="input"
                                >
                            </div>
                            <div class="field" style="margin-bottom:0;">
                                <label for="period_end">Доступен до</label>
                                <input
                                    type="datetime-local"
                                    id="period_end"
                                    name="period_end"
                                    class="input"
                                >
                                <p class="hint">Оставьте пустым — без ограничения.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="form-footer">
                        <span class="form-footer-hint">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 8v4M12 16h.01"/>
                            </svg>
                            После создания вы сможете добавить вопросы
                        </span>
                        <button type="submit" class="btn btn-primary">
                            Создать и добавить вопросы
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    <script>
        // Unlimited attempts toggle
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

        // Randomize questions visibility
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
