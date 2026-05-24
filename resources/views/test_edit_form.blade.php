<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование теста</title>

    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <style>
        body{
            margin:0;
            padding:0;
        }
        </style>
</head>
<body>


<div class="layout">

    {{-- Sidebar --}}
    <aside class="sidebar">

        <p class="sidebar-section-title">
            Навигация
        </p>

        <a href="{{ route('tests.show', $test) }}" class="sidebar-link">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            К вопросам теста
        </a>

        <a href="{{ route('home') }}" class="sidebar-link">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            </svg>
            Все курсы
        </a>

        <p class="sidebar-section-title" style="margin-top: 2rem;">
            Тест
        </p>

        <div style="padding: 0 0.75rem;">
            <p style="font-size: 13px; font-weight: 600; color: var(--gray-800); line-height: 1.4;">
                {{ $test->title }}
            </p>
        </div>

    </aside>

    {{-- Main --}}
    <main class="main">

        {{-- Breadcrumb --}}
        <nav style="display:flex; align-items:center; gap:8px; margin-bottom:1.75rem; font-size:13px; color:var(--color-text-muted);">

            <a href="{{ route('home') }}"
               style="color:var(--color-text-muted); text-decoration:none; transition:color .2s;"
               onmouseover="this.style.color='var(--teal-600)'"
               onmouseout="this.style.color='var(--color-text-muted)'">
                Курсы
            </a>

            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 18l6-6-6-6"/>
            </svg>

            <a href="{{ route('tests.show', $test) }}"
               style="color:var(--color-text-muted); text-decoration:none; transition:color .2s;"
               onmouseover="this.style.color='var(--teal-600)'"
               onmouseout="this.style.color='var(--color-text-muted)'">
                {{ $test->title }}
            </a>

            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 18l6-6-6-6"/>
            </svg>

            <span style="color:var(--gray-600); font-weight:500;">
                Настройки теста
            </span>

        </nav>

        {{-- Header --}}
        <div class="page-header">
            <h1 class="page-header__title">
                Редактирование теста
            </h1>
        </div>

        <div style="max-width: 760px;">

            <div class="panel" style="padding: 2rem;">

                {{-- Badge --}}
                <div style="
                    display:flex;
                    align-items:center;
                    gap:10px;
                    padding:10px 14px;
                    background:var(--teal-50);
                    border-radius:var(--r-md);
                    margin-bottom:2rem;
                    border:1px solid var(--teal-100);
                ">
                    <svg width="16" height="16" fill="none" stroke="var(--teal-600)" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>

                    <span style="font-size:13px; font-weight:600; color:var(--teal-700);">
                        Настройки теста и параметры прохождения
                    </span>
                </div>

                {{-- Errors --}}
                @if ($errors->any())
                    <div style="
                        background:#ffebee;
                        border:1px solid #ffcdd2;
                        border-radius:var(--r-md);
                        padding:12px 16px;
                        margin-bottom:1.5rem;
                    ">
                        <p style="
                            font-size:13px;
                            font-weight:600;
                            color:var(--red-500);
                            margin-bottom:6px;
                        ">
                            Пожалуйста, исправьте ошибки:
                        </p>

                        <ul style="
                            list-style:none;
                            padding:0;
                            margin:0;
                            display:flex;
                            flex-direction:column;
                            gap:4px;
                        ">
                            @foreach ($errors->all() as $error)
                                <li style="font-size:13px; color:var(--red-500);">
                                    • {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('tests.update-settings', $test) }}"
                      method="POST">

                    @csrf
                    @method('PUT')

                    {{-- Title --}}
                    <div style="margin-bottom:1.5rem;">

                        <label for="title"
                               style="
                                   display:block;
                                   font-size:13px;
                                   font-weight:600;
                                   color:var(--gray-700);
                                   margin-bottom:8px;
                               ">
                            Название теста
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            required
                            value="{{ old('title', $test->title) }}"
                            style="
                                width:100%;
                                padding:10px 14px;
                                border:1px solid var(--color-border);
                                border-radius:var(--r-md);
                                font-size:14px;
                                font-family:var(--font-body);
                                background:var(--color-surface);
                                transition:border-color .2s, box-shadow .2s;
                                outline:none;
                            "
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >

                    </div>

                    {{-- Description --}}
                    <div style="margin-bottom:1.5rem;">

                        <label for="description"
                               style="
                                   display:block;
                                   font-size:13px;
                                   font-weight:600;
                                   color:var(--gray-700);
                                   margin-bottom:8px;
                               ">
                            Описание теста
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            style="
                                width:100%;
                                padding:12px 14px;
                                border:1px solid var(--color-border);
                                border-radius:var(--r-md);
                                font-size:14px;
                                font-family:var(--font-body);
                                resize:vertical;
                                min-height:120px;
                                background:var(--color-surface);
                                transition:border-color .2s, box-shadow .2s;
                                outline:none;
                            "
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >{{ old('description', $test->description) }}</textarea>

                    </div>

                    {{-- Attempts --}}
                    <div style="margin-bottom:1.5rem;">

                        <label for="max_attempts"
                               style="
                                   display:block;
                                   font-size:13px;
                                   font-weight:600;
                                   color:var(--gray-700);
                                   margin-bottom:8px;
                               ">
                            Максимальное количество попыток
                        </label>

                        <input
                            type="number"
                            id="max_attempts"
                            name="max_attempts"
                            min="1"
                            value="{{ old('max_attempts', $test->max_attempts > 0 ? $test->max_attempts : 1) }}"
                            {{ $test->max_attempts === 0 ? 'disabled' : '' }}
                            style="
                                width:100%;
                                padding:10px 14px;
                                border:1px solid var(--color-border);
                                border-radius:var(--r-md);
                                font-size:14px;
                                background:var(--color-surface);
                                transition:border-color .2s, box-shadow .2s;
                                outline:none;
                            "
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >

                        <label style="
                            display:flex;
                            align-items:center;
                            gap:10px;
                            margin-top:12px;
                            font-size:14px;
                            color:var(--gray-700);
                            cursor:pointer;
                        ">
                            <input
                                type="checkbox"
                                id="unlimited_attempts"
                                name="unlimited_attempts"
                                value="1"
                                {{ $test->max_attempts === 0 ? 'checked' : '' }}
                            >

                            Неограниченное количество попыток
                        </label>

                    </div>

                    {{-- Randomize --}}
                    <div style="margin-bottom:1.5rem;">

                        <label style="
                            display:block;
                            font-size:13px;
                            font-weight:600;
                            color:var(--gray-700);
                            margin-bottom:8px;
                        ">
                            Порядок вопросов
                        </label>

                        <div id="randomize_questions_block">

                            <label style="
                                display:flex;
                                align-items:center;
                                gap:10px;
                                font-size:14px;
                                color:var(--gray-700);
                                cursor:pointer;
                            ">
                                <input
                                    type="checkbox"
                                    name="randomize_questions"
                                    value="1"
                                    {{ $test->randomize_questions ? 'checked' : '' }}
                                >

                                Случайно перемешивать вопросы
                            </label>

                        </div>

                    </div>

                    {{-- Display mode --}}
                    <div style="margin-bottom:1.5rem;">

                        <label for="display_mode"
                               style="
                                   display:block;
                                   font-size:13px;
                                   font-weight:600;
                                   color:var(--gray-700);
                                   margin-bottom:8px;
                               ">
                            Режим отображения вопросов
                        </label>

                        <select
                            id="display_mode"
                            name="display_mode"
                            style="
                                width:100%;
                                padding:10px 14px;
                                border:1px solid var(--color-border);
                                border-radius:var(--r-md);
                                font-size:14px;
                                background:var(--color-surface);
                                transition:border-color .2s, box-shadow .2s;
                                outline:none;
                            "
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >
                            <option value="single_page" {{ $test->display_mode === 'single_page' ? 'selected' : '' }}>
                                Каждый вопрос на новой странице
                            </option>

                            <option value="paged" {{ $test->display_mode === 'paged' ? 'selected' : '' }}>
                                Несколько вопросов на странице
                            </option>

                        </select>

                    </div>

                    {{-- Time limit --}}
                    <div style="margin-bottom:1.5rem;">

                        <label for="time_limit"
                               style="
                                   display:block;
                                   font-size:13px;
                                   font-weight:600;
                                   color:var(--gray-700);
                                   margin-bottom:8px;
                               ">
                            Ограничение по времени (минуты)
                        </label>

                        <input
                            type="number"
                            id="time_limit"
                            name="time_limit"
                            min="0"
                            value="{{ old('time_limit', $test->time_limit ?? 0) }}"
                            style="
                                width:100%;
                                padding:10px 14px;
                                border:1px solid var(--color-border);
                                border-radius:var(--r-md);
                                font-size:14px;
                                background:var(--color-surface);
                                transition:border-color .2s, box-shadow .2s;
                                outline:none;
                            "
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >

                        <p style="
                            font-size:12px;
                            color:var(--color-text-muted);
                            margin-top:8px;
                        ">
                            Укажите 0, если ограничение не требуется
                        </p>

                    </div>

                    {{-- Period start --}}
                    <div style="margin-bottom:1.5rem;">

                        <label for="period_start"
                               style="
                                   display:block;
                                   font-size:13px;
                                   font-weight:600;
                                   color:var(--gray-700);
                                   margin-bottom:8px;
                               ">
                            Доступен с
                        </label>

                        <input
                            type="datetime-local"
                            id="period_start"
                            name="period_start"
                            value="{{ old('period_start', $test->period_start ? $test->period_start->format('Y-m-d\TH:i') : '') }}"
                            style="
                                width:100%;
                                padding:10px 14px;
                                border:1px solid var(--color-border);
                                border-radius:var(--r-md);
                                font-size:14px;
                                background:var(--color-surface);
                                transition:border-color .2s, box-shadow .2s;
                                outline:none;
                            "
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >

                    </div>

                    {{-- Period end --}}
                    <div style="margin-bottom:2rem;">

                        <label for="period_end"
                               style="
                                   display:block;
                                   font-size:13px;
                                   font-weight:600;
                                   color:var(--gray-700);
                                   margin-bottom:8px;
                               ">
                            Доступен до
                        </label>

                        <input
                            type="datetime-local"
                            id="period_end"
                            name="period_end"
                            value="{{ old('period_end', $test->period_end ? $test->period_end->format('Y-m-d\TH:i') : '') }}"
                            style="
                                width:100%;
                                padding:10px 14px;
                                border:1px solid var(--color-border);
                                border-radius:var(--r-md);
                                font-size:14px;
                                background:var(--color-surface);
                                transition:border-color .2s, box-shadow .2s;
                                outline:none;
                            "
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >

                    </div>

                    {{-- Actions --}}
                    <div style="display:flex; align-items:center; gap:.75rem;">

                        <button type="submit"
                                class="btn btn-primary"
                                style="padding:10px 24px;">

                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>

                            Сохранить изменения
                        </button>

                        <a href="{{ route('tests.show', $test) }}"
                           class="btn btn-ghost">
                            Отмена
                        </a>

                    </div>

                </form>

            </div>

            {{-- Hint --}}
            <div style="
                display:flex;
                align-items:flex-start;
                gap:10px;
                margin-top:1rem;
                padding:12px 14px;
                background:var(--sky-50);
                border-radius:var(--r-md);
                border:1px solid var(--sky-100);
            ">
                <svg width="16" height="16" fill="none" stroke="var(--sky-500)" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>

                <p style="
                    font-size:13px;
                    color:var(--sky-700);
                    line-height:1.5;
                ">
                    Изменения настроек теста применяются сразу после сохранения.
                </p>
            </div>

        </div>

    </main>
</div>

<script>
    const checkbox = document.getElementById('unlimited_attempts');
    const numberInput = document.getElementById('max_attempts');

    checkbox.addEventListener('change', function() {
        if (this.checked) {
            numberInput.disabled = true;
            numberInput.value = 0;
        } else {
            numberInput.disabled = false;
            numberInput.value = 1;
        }
    });

    document.addEventListener('DOMContentLoaded', function () {

        const displayMode = document.getElementById('display_mode');
        const randomizeBlock = document.getElementById('randomize_questions_block');
        const randomizeCheckbox = randomizeBlock.querySelector('input[type="checkbox"]');

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
    });
</script>

</body>
</html>
