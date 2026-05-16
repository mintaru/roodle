@extends('layout')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="server-time" content="{{ now()->timestamp * 1000 }}">
    <meta name="test-start-time" content="{{ $attempt->started_at ? $attempt->started_at->timestamp * 1000 : now()->timestamp * 1000 }}">
    <link href="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.umd.min.js"></script>
    <style>
        /* ── RESET & BASE ── */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: var(--font-body, 'Manrope', sans-serif);
            background: var(--color-bg, #f8fafb);
            color: var(--color-text-primary, #111720);
            line-height: 1.6;
        }

        /* ── LAYOUT ── */
        .test-shell {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }

        /* ── TOPBAR ── */
        .test-topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255,255,255,.94);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--color-border, #e2e8ed);
            height: 60px;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            gap: 1rem;
        }

        .test-topbar__title {
            font-size: 15px;
            font-weight: 700;
            color: var(--gray-800, #1e2530);
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            z-index: 50;

        }

        .test-topbar__desc {
            font-size: 12px;
            color: var(--color-text-muted, #9eaab7);
            flex-shrink: 0;
            display: none;
            z-index: 50;

        }

        @media (min-width: 640px) {
            .test-topbar__desc { display: block; }
        }

        /* ── TIMER ── */
        .timer-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff8e1;
            border: 1.5px solid #ffca28;
            border-radius: 999px;
            padding: 5px 14px;
            flex-shrink: 0;
        }

        .timer-pill svg { color: #e65100; flex-shrink: 0; }

        .timer-pill__value {
            font-size: 15px;
            font-weight: 800;
            color: #c62828;
            font-variant-numeric: tabular-nums;
            letter-spacing: .5px;
            min-width: 48px;
            text-align: center;
        }

        .timer-pill.urgent {
            background: #ffebee;
            border-color: #ef5350;
            animation: pulse-border 1s ease infinite;
        }

        @keyframes pulse-border {
            0%, 100% { border-color: #ef5350; }
            50% { border-color: #c62828; }
        }

        /* ── SIDEBAR TOGGLE BTN ── */
        .sidebar-toggle-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: var(--r-md, 12px);
            border: 1.5px solid var(--color-border, #e2e8ed);
            background: var(--color-surface, #fff);
            cursor: pointer;
            transition: .2s ease;
            flex-shrink: 0;
            color: var(--gray-600, #4a5668);
        }

        .sidebar-toggle-btn:hover {
            border-color: var(--teal-400, #26c6b8);
            color: var(--teal-600, #009e90);
            background: var(--teal-50, #e0f7f4);
        }

        /* ── BODY (sidebar + content) ── */
        .test-body {
            display: flex;
            flex: 1;
            position: relative;
        }

        /* ── SIDEBAR ── */
        .test-sidebar {
            width: 280px;
            background: var(--color-surface, #fff);
            border-right: 1px solid var(--color-border, #e2e8ed);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 60px;
            height: calc(100vh - 60px);
            overflow-y: auto;
            flex-shrink: 0;
            transition: width .3s ease, opacity .3s ease, transform .3s ease;
            z-index: 100;
        }

        .test-sidebar.collapsed {
            width: 0;
            opacity: 0;
            pointer-events: none;
            overflow: hidden;
        }

        /* On mobile: overlay sidebar */
        @media (max-width: 768px) {
            .test-sidebar {
                position: fixed;
                top: 60px;
                left: 0;
                height: calc(100vh - 60px);
                width: 280px;
                box-shadow: 4px 0 24px rgba(0,0,0,.12);
                transform: translateX(0);
            }

            .test-sidebar.collapsed {
                width: 280px;
                opacity: 0;
                transform: translateX(-100%);
                pointer-events: none;
            }
        }

        .sidebar-inner {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            min-width: 280px;
        }

        .sidebar-section-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--color-text-muted, #9eaab7);
            margin-bottom: .25rem;
        }

        /* Question number grid */
        .q-nav-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 6px;
        }

        .q-nav-btn {
            aspect-ratio: 1;
            border-radius: var(--r-md, 12px);
            border: 1.5px solid var(--color-border, #e2e8ed);
            background: var(--color-surface, #fff);
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-600, #4a5668);
            cursor: pointer;
            transition: .2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            font-family: var(--font-body, 'Manrope', sans-serif);
        }

        .q-nav-btn:hover {
            border-color: var(--teal-400, #26c6b8);
            color: var(--teal-700, #00837a);
            background: var(--teal-50, #e0f7f4);
        }

        .q-nav-btn.active {
            background: var(--teal-500, #00b5a5);
            border-color: var(--teal-500, #00b5a5);
            color: #fff;
            box-shadow: 0 2px 8px rgba(0,181,165,.35);
        }

        .q-nav-btn.answered {
            border-color: var(--teal-200, #7ddfd5);
            background: var(--teal-50, #e2f7e0);
            color: var(--teal-700, #00837a);
        }

        .q-nav-btn.answered::after {
            content: '';
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--teal-500, #00b5a5);
        }

        .q-nav-btn.active.answered::after {
            background: rgba(255, 204, 0, 0.7);
        }

        /* Progress in sidebar */
        .sidebar-progress {
            background: var(--gray-100, #f0f3f5);
            border-radius: var(--r-lg, 16px);
            padding: 1rem;
        }

        .sidebar-progress__nums {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700, #333d4a);
            margin-bottom: 8px;
        }

        .sidebar-progress__bar {
            height: 6px;
            background: var(--gray-200, #e2e8ed);
            border-radius: 999px;
            overflow: hidden;
        }

        .sidebar-progress__fill {
            height: 100%;
            background: linear-gradient(90deg, var(--teal-400, #26c6b8), var(--sky-400, #29aff5));
            border-radius: 999px;
            transition: width .5s ease;
        }

        .sidebar-progress__label {
            font-size: 11px;
            color: var(--color-text-muted, #9eaab7);
            margin-top: 6px;
        }

        /* Submit btn in sidebar */
        .sidebar-submit-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 11px 18px;
            background: var(--teal-500, #00b5a5);
            color: #fff;
            border: none;
            border-radius: var(--r-full, 999px);
            font-size: 14px;
            font-weight: 700;
            font-family: var(--font-body, 'Manrope', sans-serif);
            cursor: pointer;
            transition: .2s ease;
            box-shadow: 0 4px 16px rgba(0,181,165,.3);
        }

        .sidebar-submit-btn:hover {
            background: var(--teal-600, #009e90);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0,181,165,.4);
        }

        /* Legend */
        .sidebar-legend {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--color-text-secondary, #6b7a89);
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: var(--r-sm, 8px);
            flex-shrink: 0;
        }

        .legend-dot--empty { background: var(--gray-200, #e2e8ed); }
        .legend-dot--answered { background: var(--teal-400, #26c6b8); }
        .legend-dot--active { background: var(--teal-500, #00b5a5); }

        /* ── MAIN CONTENT ── */
        .test-main {
            flex: 1;
            padding: 2rem 1.5rem;
            max-width: 1250px;
            margin: 0 auto;
            width: 100%;
            transition: .3s ease;
        }

        /* ── QUESTION CARD ── */
        .question-card {
            background: var(--color-surface, #fff);
            border: 1px solid var(--color-border, #e2e8ed);
            border-radius: var(--r-xl, 20px);
            box-shadow: 0 4px 20px rgba(0,0,0,.06);
            overflow: hidden;
            display: none;
            animation: slideIn .22s ease;
        }

        .question-card.visible {
            display: block;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: none; }
        }

        .question-card__header {
            padding: 1.5rem 1.75rem 1.25rem;
            border-bottom: 1px solid var(--color-border, #e2e8ed);
            background: var(--gray-50, #f8fafb);
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .question-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: var(--r-md, 12px);
            background: var(--teal-500, #00b5a5);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .question-text {
            font-size: 16px;
            font-weight: 600;
            color: var(--gray-800, #1e2530);
            line-height: 1.55;
            flex: 1;
            margin-top:6px;
        }

        .question-card__body {
            padding: 1.5rem 1.75rem;
        }

        /* ── OPTIONS ── */
        .option-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .option-label {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 16px;
            border: 1.5px solid var(--color-border, #e2e8ed);
            border-radius: var(--r-lg, 16px);
            cursor: pointer;
            transition: .2s ease;
            user-select: none;
            background: var(--color-surface, #fff);
        }

        .option-label:hover {
            border-color: var(--teal-300, #80cbc4);
            background: var(--teal-50, #e0f7f4);
        }

        .option-label input[type="radio"],
        .option-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--teal-500, #00b5a5);
            flex-shrink: 0;
            cursor: pointer;
        }

        .option-label:has(input:checked) {
            border-color: var(--teal-500, #00b5a5);
            background: var(--teal-50, #e0f7f4);
        }

        .option-label span {
            font-size: 14.5px;
            color: var(--gray-700, #333d4a);
            line-height: 1.5;
        }

        /* ── SHORT ANSWER ── */
        .short-answer-wrap { }

        .short-answer-wrap label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-text-muted, #9eaab7);
            text-transform: uppercase;
            letter-spacing: .7px;
            margin-bottom: 8px;
        }

        .short-answer-input {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid var(--color-border, #e2e8ed);
            border-radius: var(--r-lg, 16px);
            font-size: 15px;
            font-family: var(--font-body, 'Manrope', sans-serif);
            color: var(--color-text-primary, #111720);
            background: var(--color-surface, #fff);
            transition: .2s ease;
        }

        .short-answer-input:focus {
            outline: none;
            border-color: var(--teal-400, #26c6b8);
            box-shadow: 0 0 0 3px rgba(0,181,165,.12);
        }

        /* ── RICH TEXT ── */
        .rich-text-wrap label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-text-muted, #9eaab7);
            text-transform: uppercase;
            letter-spacing: .7px;
            margin-bottom: 8px;
        }

        trix-editor.rich-text-answer-input {
            min-height: 160px;
            width: 100%;
            border: 1.5px solid var(--color-border, #e2e8ed);
            border-radius: var(--r-lg, 16px);
            padding: 12px 16px;
            font-size: 15px;
            font-family: var(--font-body, 'Manrope', sans-serif);
            transition: .2s ease;
        }

        trix-editor.rich-text-answer-input:focus {
            outline: none;
            border-color: var(--teal-400, #26c6b8);
            box-shadow: 0 0 0 3px rgba(0,181,165,.12);
        }

        trix-editor ul, trix-editor ol,
        .trix-content ul, .trix-content ol {
            list-style-type: disc;
            list-style-position: outside;
            margin-left: 1.5rem;
        }

        trix-toolbar {
            border-radius: var(--r-md, 12px) var(--r-md, 12px) 0 0;
        }

        /* ── FILL DROPDOWN ── */
        .fill-in-dropdown-select-inline {
            display: inline-block;
            padding: 4px 10px;
            margin: 0 4px;
            border: 1.5px solid var(--teal-300, #80cbc4);
            border-radius: 8px;
            font-size: 14px;
            font-family: var(--font-body, 'Manrope', sans-serif);
            background: var(--teal-50, #e0f7f4);
            color: var(--teal-800, #006560);
            font-weight: 600;
            cursor: pointer;
            outline: none;
            transition: .2s ease;
        }

        .fill-in-dropdown-select-inline:focus {
            border-color: var(--teal-500, #00b5a5);
            box-shadow: 0 0 0 2px rgba(0,181,165,.15);
        }

        .original-text-hint {
            margin-top: 12px;
            font-size: 12px;
            color: var(--color-text-muted, #9eaab7);
            background: var(--gray-100, #f0f3f5);
            border-radius: var(--r-md, 12px);
            padding: 8px 12px;
            line-height: 1.5;
        }

        /* ── NAV BUTTONS ── */
        .question-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.5rem;
        }

        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: var(--r-full, 999px);
            font-size: 13px;
            font-weight: 600;
            font-family: var(--font-body, 'Manrope', sans-serif);
            border: 1.5px solid var(--color-border, #e2e8ed);
            background: var(--color-surface, #fff);
            color: var(--gray-600, #4a5668);
            cursor: pointer;
            transition: .2s ease;
        }

        .nav-btn:hover:not(:disabled) {
            border-color: var(--teal-400, #26c6b8);
            color: var(--teal-700, #00837a);
            background: var(--teal-50, #e0f7f4);
        }

        .nav-btn:disabled {
            opacity: .35;
            cursor: not-allowed;
        }

        .nav-btn#prevBtn:disabled {
            display: none;
        }

        .nav-btn--primary {
            background: var(--teal-500, #00b5a5);
            border-color: var(--teal-500, #00b5a5);
            color: #fff;
            box-shadow: 0 2px 10px rgba(0,181,165,.25);
        }

        .nav-btn--primary:hover:not(:disabled) {
            border-color: var(--teal-600, #009e90);
        }

        /* ── MOBILE OVERLAY ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            top: 60px;
            background: rgba(0,0,0,.35);
            z-index: 99;
        }

        @media (max-width: 768px) {
            .sidebar-overlay.active { display: block; }
        }

        /* ── CONFIRM MODAL ── */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-backdrop.active {
            display: flex;
        }

        .modal-box {
            background: var(--color-surface, #fff);
            border-radius: var(--r-2xl, 28px);
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 24px 60px rgba(0,0,0,.2);
            animation: modalIn .2s ease;
        }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(.95); }
            to   { opacity: 1; transform: none; }
        }

        .modal-icon {
            width: 52px;
            height: 52px;
            border-radius: var(--r-xl, 20px);
            background: #fff3e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }

        .modal-box h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--gray-800, #1e2530);
            margin-bottom: .5rem;
        }

        .modal-box p {
            font-size: 14px;
            color: var(--color-text-secondary, #6b7a89);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .modal-stats {
            background: var(--gray-50, #f8fafb);
            border-radius: var(--r-lg, 16px);
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1.5rem;
        }

        .modal-stat {
            text-align: center;
            flex: 1;
        }

        .modal-stat__val {
            font-size: 22px;
            font-weight: 800;
            color: var(--teal-600, #009e90);
        }

        .modal-stat__lbl {
            font-size: 11px;
            color: var(--color-text-muted, #9eaab7);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
        }

        .modal-btn {
            flex: 1;
            padding: 11px;
            border-radius: var(--r-full, 999px);
            font-size: 14px;
            font-weight: 700;
            font-family: var(--font-body, 'Manrope', sans-serif);
            border: none;
            cursor: pointer;
            transition: .2s ease;
        }

        .modal-btn--cancel {
            background: var(--gray-100, #f0f3f5);
            color: var(--gray-600, #4a5668);
        }

        .modal-btn--cancel:hover { background: var(--gray-200, #e2e8ed); }

        .modal-btn--confirm {
            background: var(--teal-500, #00b5a5);
            color: #fff;
            box-shadow: 0 4px 14px rgba(0,181,165,.3);
        }

        .modal-btn--confirm:hover {
            background: var(--teal-600, #009e90);
            transform: translateY(-1px);
        }

        /* ── SCROLLBAR ── */
        .test-sidebar::-webkit-scrollbar { width: 4px; }
        .test-sidebar::-webkit-scrollbar-track { background: transparent; }
        .test-sidebar::-webkit-scrollbar-thumb { background: var(--gray-200, #e2e8ed); border-radius: 2px; }
    </style>
@endsection

@section('content')
@php
    $totalQuestions = $test->questions->count();
@endphp

<div class="test-shell">

    {{-- ─────────── TOP BAR ─────────── --}}
    <header class="test-topbar">
        {{-- sidebar toggle --}}
        <button class="sidebar-toggle-btn" id="sidebarToggle" title="Навигация по вопросам">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- title --}}
        <h1 class="test-topbar__title">{{ $test->title }}</h1>

        {{-- description (desktop) --}}
        @if($test->description)
            <span class="test-topbar__desc">{{ Str::limit($test->description, 60) }}</span>
        @endif

        {{-- timer --}}
        @if ($test->time_limit > 0)
            <div class="timer-pill" id="timerPill">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                </svg>
                <span class="timer-pill__value" id="timer">{{ floor($test->time_limit) }}:00</span>
            </div>
        @endif
    </header>

    {{-- ─────────── BODY ─────────── --}}
    <div class="test-body">

        {{-- Mobile overlay --}}
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        {{-- ─────────── SIDEBAR ─────────── --}}
        <aside class="test-sidebar" id="testSidebar">
            <div class="sidebar-inner">

                {{-- Progress --}}
                <div>
                    <div class="sidebar-section-label">Прогресс</div>
                    <div class="sidebar-progress">
                        <div class="sidebar-progress__nums">
                            <span>Отвечено: <strong id="answeredCount">0</strong></span>
                            <span>Всего: <strong>{{ $totalQuestions }}</strong></span>
                        </div>
                        <div class="sidebar-progress__bar">
                            <div class="sidebar-progress__fill" id="progressFill" style="width: 0%"></div>
                        </div>
                        <div class="sidebar-progress__label" id="progressLabel">Ответьте на все вопросы</div>
                    </div>
                </div>

                {{-- Questions grid --}}
                <div>
                    <div class="sidebar-section-label">Вопросы</div>
                    <div class="q-nav-grid" id="qNavGrid">
                        @foreach ($test->questions as $i => $question)
                            <button
                                class="q-nav-btn {{ $i === 0 ? 'active' : '' }}"
                                data-q-index="{{ $i }}"
                                title="Вопрос {{ $i + 1 }}"
                            >{{ $i + 1 }}</button>
                        @endforeach
                    </div>
                </div>

                {{-- Legend --}}
                <div>
                    <div class="sidebar-section-label">Обозначения</div>
                    <div class="sidebar-legend">
                        <div class="legend-item">
                            <div class="legend-dot legend-dot--empty"></div>
                            <span>Не отвечено</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot legend-dot--answered"></div>
                            <span>Отвечено</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot legend-dot--active"></div>
                            <span>Текущий вопрос</span>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <button class="sidebar-submit-btn" id="sidebarSubmitBtn" type="button">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Завершить тест
                </button>

            </div>
        </aside>

        {{-- ─────────── MAIN ─────────── --}}
        <main class="test-main">
            <form action="/tests/{{ $test->id }}/result" method="POST" id="testForm">
                @csrf

                @foreach ($test->questions as $i => $question)
                    @php
                        $questionDisplay = $question->question_text;
                        if ($question->question_type === 'fill_in_dropdown') {
                            $dropdownsByBlank = [];
                            foreach ($question->options as $option) {
                                $data = json_decode($option->option_text, true);
                                if (!isset($dropdownsByBlank[$data['blank_id']])) {
                                    $dropdownsByBlank[$data['blank_id']] = [];
                                }
                                $dropdownsByBlank[$data['blank_id']][] = [
                                    'id' => $option->id,
                                    'text' => $data['text'],
                                ];
                            }

                            foreach ($dropdownsByBlank as $blankId => $options) {
                                $savedValue = '';
                                if (isset($savedAnswers[$question->id]) && is_array($savedAnswers[$question->id])) {
                                    $savedValue = $savedAnswers[$question->id][$blankId] ?? '';
                                }

                                $selectHTML = '<select class="fill-in-dropdown-select-inline" data-question-id="' . $question->id . '" data-blank-id="' . $blankId . '">';
                                $selectHTML .= '<option value="">—выберите—</option>';
                                foreach ($options as $option) {
                                    $selectedAttr = ($savedValue == $option['id']) ? 'selected' : '';
                                    $selectHTML .= '<option value="' . $option['id'] . '" ' . $selectedAttr . '>' . htmlspecialchars($option['text'], ENT_QUOTES, 'UTF-8') . '</option>';
                                }
                                $selectHTML .= '</select>';

                                $questionDisplay = str_replace('{' . $blankId . '}', $selectHTML, $questionDisplay);
                            }
                        }
                    @endphp

                    <div class="question-card {{ $i === 0 ? 'visible' : '' }}" data-q-index="{{ $i }}" id="question-{{ $i }}">
                        <div class="question-card__header">
                            <div class="question-badge">{{ $i + 1 }}</div>
                            <p class="question-text">{!! $questionDisplay !!}</p>
                            <button type="button" class="nav-btn clearCurrentBtn" title="Очистить ответ на текущий вопрос">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>

                        <div class="question-card__body">

                            {{-- SHORT ANSWER --}}
                            @if ($question->question_type === 'short_answer')
                                @php
                                    $savedText = $savedAnswers[$question->id] ?? '';
                                @endphp
                                <div class="short-answer-wrap">
                                    <label>Ваш ответ</label>
                                    <input
                                        type="text"
                                        name="text_answers[{{ $question->id }}]"
                                        class="short-answer-input text-answer-input"
                                        placeholder="Введите ответ..."
                                        value="{{ $savedText }}"
                                        data-question-id="{{ $question->id }}"
                                        data-q-index="{{ $i }}"
                                    >
                                </div>

                            {{-- RICH TEXT --}}
                            @elseif ($question->question_type === 'rich_text_answer')
                                @php
                                    $savedRichText = (isset($savedAnswers[$question->id]) && is_string($savedAnswers[$question->id])) ? $savedAnswers[$question->id] : '';
                                @endphp
                                <div class="rich-text-wrap">
                                    <label>Развёрнутый ответ</label>
                                    <input type="hidden" id="rich_text_answer_{{ $question->id }}" name="rich_text_answers[{{ $question->id }}]" value="{{ $savedRichText }}">
                                    <trix-editor
                                        input="rich_text_answer_{{ $question->id }}"
                                        data-question-id="{{ $question->id }}"
                                        data-q-index="{{ $i }}"
                                        class="rich-text-answer-input"
                                    ></trix-editor>
                                </div>

                            {{-- FILL IN DROPDOWN --}}
                            @elseif ($question->question_type === 'fill_in_dropdown')
                                @if ($question->description ?? false)
                                    <div class="original-text-hint">
                                        <strong>Исходный текст:</strong> {{ $question->question_text }}
                                    </div>
                                @endif

                            {{-- SINGLE / MULTIPLE CHOICE --}}
                            @else
                                <div class="option-list">
                                    @foreach ($question->options as $option)
                                        @php
                                            $isChecked = false;
                                            if (isset($savedAnswers[$question->id]) && is_array($savedAnswers[$question->id])) {
                                                if ($question->question_type === 'single_choice') {
                                                    $isChecked = $savedAnswers[$question->id][0] == $option->id;
                                                } else {
                                                    $isChecked = in_array($option->id, $savedAnswers[$question->id]);
                                                }
                                            }
                                        @endphp
                                        <label class="option-label">
                                            <input
                                                type="{{ $question->question_type === 'single_choice' ? 'radio' : 'checkbox' }}"
                                                name="answers[{{ $question->id }}]{{ $question->question_type === 'multiple_choice' ? '[]' : '' }}"
                                                value="{{ $option->id }}"
                                                class="answer-input"
                                                data-question-id="{{ $question->id }}"
                                                data-q-index="{{ $i }}"
                                                {{ $isChecked ? 'checked' : '' }}
                                            >
                                            <span>{{ $option->option_text }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    </div>

                @endforeach

                {{-- Navigation buttons --}}
                <div class="question-nav">
                    <button type="button" class="nav-btn" id="prevBtn">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        Предыдущий
                    </button>



                    <span style="font-size:13px;color:var(--color-text-muted);" id="qCounterLabel">
                        Вопрос <strong id="qCounterCurrent">1</strong> из <strong>{{ $totalQuestions }}</strong>
                    </span>

                    <button type="button" class="nav-btn nav-btn--primary" id="nextBtn">
                        <span id="nextBtnText">Следующий</span>
                        <svg id="nextBtnArrow" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

            </form>
        </main>

    </div>
</div>

{{-- ─────────── CONFIRM MODAL ─────────── --}}
<div class="modal-backdrop" id="confirmModal">
    <div class="modal-box">
        <div class="modal-icon">
            <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="#e65100" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>
        <h3>Завершить тест?</h3>
        <p>После отправки вы не сможете изменить ответы. Убедитесь, что ответили на все вопросы.</p>
        <div class="modal-stats">
            <div class="modal-stat">
                <div class="modal-stat__val" id="modalAnswered">0</div>
                <div class="modal-stat__lbl">Отвечено</div>
            </div>
            <div class="modal-stat">
                <div class="modal-stat__val" style="color:var(--gray-400)" id="modalUnanswered">0</div>
                <div class="modal-stat__lbl">Без ответа</div>
            </div>
            <div class="modal-stat">
                <div class="modal-stat__val" style="color:var(--sky-600)">{{ $totalQuestions }}</div>
                <div class="modal-stat__lbl">Всего</div>
            </div>
        </div>
        <div class="modal-actions">
            <button class="modal-btn modal-btn--cancel" onclick="closeModal()">Вернуться</button>
            <button class="modal-btn modal-btn--confirm" onclick="submitTest()">Завершить</button>
        </div>
    </div>
</div>

<script>
// ── Shared state (must be global so onclick="..." handlers can access it) ──
const TestApp = {
    TOTAL: {{ $totalQuestions }},
    currentQ: 0,
    answeredSet: new Set(),
};

function openModal() {
    document.getElementById('modalAnswered').textContent = TestApp.answeredSet.size;
    document.getElementById('modalUnanswered').textContent = TestApp.TOTAL - TestApp.answeredSet.size;
    document.getElementById('confirmModal').classList.add('active');
}

function closeModal() {
    document.getElementById('confirmModal').classList.remove('active');
}

function submitTest() {
    document.getElementById('testForm').submit();
}

function navigateQ(dir) {
    if (_goToQuestion) _goToQuestion(TestApp.currentQ + dir);
}

// goToQuestion is defined inside DOMContentLoaded but exposed via wrapper above
let _goToQuestion = null;

document.addEventListener('DOMContentLoaded', function () {
    const TOTAL = TestApp.TOTAL;
    let currentQ = TestApp.currentQ;
    const answeredSet = TestApp.answeredSet;

    // Pre-mark answered questions from saved answers
    @foreach ($test->questions as $i => $question)
        @if (isset($savedAnswers[$question->id]))
            @php $ans = $savedAnswers[$question->id]; @endphp
            @if (is_array($ans) && count($ans) > 0)
                answeredSet.add({{ $i }});
            @elseif (is_string($ans) && strlen($ans) > 0)
                answeredSet.add({{ $i }});
            @endif
        @endif
    @endforeach

    // ── Sidebar toggle ──
    const sidebar = document.getElementById('testSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    let sidebarOpen = window.innerWidth > 768;

    function applySidebarState() {
        if (sidebarOpen) {
            sidebar.classList.remove('collapsed');
            overlay.classList.remove('active');
        } else {
            sidebar.classList.add('collapsed');
        }
    }

    applySidebarState();

    toggleBtn.addEventListener('click', () => {
        sidebarOpen = !sidebarOpen;
        applySidebarState();
        if (sidebarOpen && window.innerWidth <= 768) {
            overlay.classList.add('active');
        } else {
            overlay.classList.remove('active');
        }
    });

    overlay.addEventListener('click', () => {
        sidebarOpen = false;
        applySidebarState();
        overlay.classList.remove('active');
    });

    // ── Navigation ──
    const cards = document.querySelectorAll('.question-card');
    const navBtns = document.querySelectorAll('.q-nav-btn');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const qCounterCurrent = document.getElementById('qCounterCurrent');

    function goToQuestion(index) {
        if (index < 0 || index >= TOTAL) return;

        cards[currentQ].classList.remove('visible');
        navBtns[currentQ].classList.remove('active');

        currentQ = index;
        TestApp.currentQ = index;

        cards[currentQ].classList.add('visible');
        navBtns[currentQ].classList.add('active');

        qCounterCurrent.textContent = currentQ + 1;
        prevBtn.disabled = currentQ === 0;

        // Update next button based on question position
        const isLastQuestion = currentQ === TOTAL - 1;
        if (isLastQuestion) {
            document.getElementById('nextBtnText').textContent = 'Завершить';
            document.getElementById('nextBtnArrow').style.display = 'none';
        } else {
            document.getElementById('nextBtnText').textContent = 'Следующий';
            document.getElementById('nextBtnArrow').style.display = 'inline';
        }

        // On mobile, close sidebar after nav
        if (window.innerWidth <= 768 && sidebarOpen) {
            sidebarOpen = false;
            applySidebarState();
            overlay.classList.remove('active');
        }
    }

    _goToQuestion = goToQuestion;

    navBtns.forEach((btn, i) => {
        btn.addEventListener('click', () => goToQuestion(i));
    });

    // Initial state
    prevBtn.disabled = true;
    prevBtn.addEventListener('click', function() {
        navigateQ(-1);
    });

    if (TOTAL > 1) {
        nextBtn.disabled = false;
        nextBtn.addEventListener('click', function() {
            if (currentQ === TOTAL - 1) {
                openModal();
            } else {
                navigateQ(1);
            }
        });
    } else {
        nextBtn.disabled = true;
    }
    updateProgress();
    renderNavBtns();

    // ── Mark answered ──
    function markAnswered(qIndex) {
        answeredSet.add(qIndex);
        updateProgress();
        renderNavBtns();
    }

    function updateProgress() {
        const count = answeredSet.size;
        document.getElementById('answeredCount').textContent = count;
        const pct = TOTAL > 0 ? Math.round((count / TOTAL) * 100) : 0;
        document.getElementById('progressFill').style.width = pct + '%';
        if (count === TOTAL) {
            document.getElementById('progressLabel').textContent = 'Все вопросы отвечены ✓';
        } else {
            document.getElementById('progressLabel').textContent = `Осталось: ${TOTAL - count}`;
        }
    }

    function renderNavBtns() {
        navBtns.forEach((btn, i) => {
            btn.classList.toggle('answered', answeredSet.has(i));
        });
    }

    // ── Answer listeners ──
    document.querySelectorAll('.answer-input').forEach(input => {
        if (answeredSet.has(parseInt(input.dataset.qIndex))) {
            // already answered — no need to re-add
        }
        input.addEventListener('change', async function () {
            markAnswered(parseInt(this.dataset.qIndex));
            try {
                const questionId = this.dataset.questionId;
                let optionIds;
                if (this.type === 'radio') {
                    optionIds = this.value;
                } else {
                    const checked = document.querySelectorAll(`input[name="answers[${questionId}][]"]:checked`);
                    optionIds = Array.from(checked).map(cb => cb.value);
                }
                await fetch(`/tests/{{ $test->id }}/save-answer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ question_id: questionId, option_id: optionIds })
                });
            } catch (e) { console.error(e); }
        });
    });

    document.querySelectorAll('.text-answer-input').forEach(input => {
        input.addEventListener('input', function () {
            if (this.value.trim()) markAnswered(parseInt(this.dataset.qIndex));
        });
        input.addEventListener('change', async function () {
            try {
                await fetch(`/tests/{{ $test->id }}/save-answer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ question_id: this.dataset.questionId, answer_text: this.value })
                });
            } catch (e) { console.error(e); }
        });
    });

    document.querySelectorAll('.rich-text-answer-input').forEach(editor => {
        editor.addEventListener('trix-change', async function () {
            const qIndex = parseInt(this.dataset.qIndex);
            const inputId = this.getAttribute('input');
            const val = document.getElementById(inputId).value;
            if (val && val !== '<div></div>') markAnswered(qIndex);
            try {
                await fetch(`/tests/{{ $test->id }}/save-answer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ question_id: this.dataset.questionId, rich_text_answer: val })
                });
            } catch (e) { console.error(e); }
        });
    });

    document.querySelectorAll('.fill-in-dropdown-select-inline').forEach(select => {
        select.addEventListener('change', async function () {
            const questionId = this.dataset.questionId;
            // find q-index from the card
            const card = this.closest('.question-card');
            if (card) markAnswered(parseInt(card.dataset.qIndex));

            const filledAnswers = {};
            document.querySelectorAll(`.fill-in-dropdown-select-inline[data-question-id="${questionId}"]`).forEach(sel => {
                if (sel.value) filledAnswers[sel.dataset.blankId] = parseInt(sel.value);
            });
            try {
                await fetch(`/tests/{{ $test->id }}/save-answer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ question_id: questionId, fill_in_dropdown_answers: filledAnswers })
                });
            } catch (e) { console.error(e); }
        });
    });

    // Block file attachments in Trix
    document.addEventListener('trix-file-accept', e => e.preventDefault());
    document.addEventListener('trix-attachment-add', e => { if (e.attachment) e.attachment.remove(); });

    // ── Clear current answer ──
    document.querySelectorAll('.clearCurrentBtn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const currentCard = cards[currentQ];
            const questionId = currentCard.querySelector('.answer-input, .text-answer-input, .rich-text-answer-input, .fill-in-dropdown-select-inline')?.dataset.questionId;

            // Clear radio buttons and checkboxes for current question
            currentCard.querySelectorAll('.answer-input').forEach(input => {
                input.checked = false;
            });

            // Clear text input for current question
            const textInput = currentCard.querySelector('.text-answer-input');
            if (textInput) textInput.value = '';

            // Clear Trix editor for current question
            const richEditor = currentCard.querySelector('.rich-text-answer-input');
            if (richEditor) {
                const inputId = richEditor.getAttribute('input');
                document.getElementById(inputId).value = '';
                richEditor.editor.setSelectedRange([0, richEditor.editor.getDocument().getLength()]);
                richEditor.editor.deleteInDirection('forward');
            }

            // Clear dropdowns for current question
            currentCard.querySelectorAll('.fill-in-dropdown-select-inline').forEach(select => {
                select.value = '';
            });

            // Remove current question from answered set
            answeredSet.delete(currentQ);
            updateProgress();
            renderNavBtns();

            // Сохраняем очищение на сервер
            try {
                await fetch(`/tests/{{ $test->id }}/clear-answer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ question_id: questionId })
                });
            } catch (e) { console.error(e); }
        });
    });

    // ── Submit modal ──
    document.getElementById('sidebarSubmitBtn').addEventListener('click', openModal);

    document.getElementById('confirmModal').addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });

    // ── Timer ──
    @if ($test->time_limit > 0)
        const serverTimeMeta = parseInt(document.querySelector('meta[name="server-time"]')?.content || 0);
        const testStartTimeMeta = parseInt(document.querySelector('meta[name="test-start-time"]')?.content || 0);
        const clientTimeAtLoad = Date.now();
        const timerEl = document.getElementById('timer');
        const timerPill = document.getElementById('timerPill');
        const timeLimitMs = {{ $test->time_limit }} * 60 * 1000;
        let timerInterval;

        function updateTimer() {
            const elapsed = (serverTimeMeta + (Date.now() - clientTimeAtLoad)) - testStartTimeMeta;
            const leftMs = Math.max(0, timeLimitMs - elapsed);
            const leftSec = Math.round(leftMs / 1000);
            const m = Math.floor(leftSec / 60);
            const s = leftSec % 60;
            timerEl.textContent = `${m}:${String(s).padStart(2, '0')}`;
            if (leftSec <= 60) timerPill.classList.add('urgent');
            if (leftSec <= 0) {
                timerEl.textContent = '00:00';
                clearInterval(timerInterval);
                document.getElementById('testForm').submit();
            }
        }

        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);
    @endif
});
</script>
@endsection
