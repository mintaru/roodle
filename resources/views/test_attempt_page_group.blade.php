@extends('layout')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="server-time" content="{{ $serverTime }}">
    <meta name="test-start-time" content="{{ $activeAttempt->started_at->timestamp * 1000 }}">
    <meta name="test-id" content="{{ $test->id }}">
    <link href="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.umd.min.js"></script>
    <style>
        /* ── RESET & BASE ── */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

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
            background: rgba(255, 255, 255, .94);
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
            .test-topbar__desc {
                display: block;
            }
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

        .timer-pill svg {
            color: #e65100;
            flex-shrink: 0;
        }

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

        /* ── BODY ── */
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

        @media (max-width: 768px) {
            .test-sidebar {
                position: fixed;
                top: 60px;
                left: 0;
                height: calc(100vh - 60px);
                width: 280px;
                box-shadow: 4px 0 24px rgba(0, 0, 0, .12);
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

        /* Page navigation in sidebar */
        .page-nav-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .page-nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--r-md, 12px);
            border: 1.5px solid var(--color-border, #e2e8ed);
            background: var(--color-surface, #fff);
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-600, #4a5668);
            text-decoration: none;
            transition: .2s ease;
        }

        .page-nav-item:hover {
            border-color: var(--teal-400, #26c6b8);
            color: var(--teal-700, #00837a);
            background: var(--teal-50, #e0f7f4);
        }

        .page-nav-item.active {
            background: var(--teal-500, #00b5a5);
            border-color: var(--teal-500, #00b5a5);
            color: #fff;
            box-shadow: 0 2px 8px rgba(0, 181, 165, .35);
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
            box-shadow: 0 4px 16px rgba(0, 181, 165, .3);
        }

        .sidebar-submit-btn:hover {
            background: var(--teal-600, #009e90);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 181, 165, .4);
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

        .legend-dot--current {
            background: var(--teal-500, #00b5a5);
        }

        .legend-dot--other {
            background: var(--gray-200, #e2e8ed);
        }

        /* ── MAIN CONTENT ── */
        .test-main {
            flex: 1;
            padding: 2rem 1.5rem;
            max-width: 1250px;
            margin: 0 auto;
            width: 100%;
        }

        /* ── QUESTION CARD ── */
        .question-card {
            background: var(--color-surface, #fff);
            border: 1px solid var(--color-border, #e2e8ed);
            border-radius: var(--r-xl, 20px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, .06);
            overflow: hidden;
            margin-bottom: 1.5rem;
            animation: slideIn .22s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: none; }
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
            margin-top: 6px;
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
            box-shadow: 0 0 0 3px rgba(0, 181, 165, .12);
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
            box-shadow: 0 0 0 3px rgba(0, 181, 165, .12);
        }

        trix-editor ul,
        trix-editor ol,
        .trix-content ul,
        .trix-content ol {
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
            box-shadow: 0 0 0 2px rgba(0, 181, 165, .15);
        }

        /* ── PAGE NAV BUTTONS ── */
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
            text-decoration: none;
        }

        .nav-btn:hover {
            border-color: var(--teal-400, #26c6b8);
            color: var(--teal-700, #00837a);
            background: var(--teal-50, #e0f7f4);
        }

        .nav-btn--primary {
            background: var(--teal-500, #00b5a5);
            border-color: var(--teal-500, #00b5a5);
            color: #fff;
            box-shadow: 0 2px 10px rgba(0, 181, 165, .25);
        }

        .nav-btn--primary:hover {
            background: var(--teal-600, #009e90);
            border-color: var(--teal-600, #009e90);
            color: #fff;
        }

        /* ── MOBILE OVERLAY ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            top: 60px;
            background: rgba(0, 0, 0, .35);
            z-index: 99;
        }

        @media (max-width: 768px) {
            .sidebar-overlay.active {
                display: block;
            }
        }

        /* ── CONFIRM MODAL ── */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
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
            box-shadow: 0 24px 60px rgba(0, 0, 0, .2);
            animation: modalIn .2s ease;
        }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(.95); }
            to { opacity: 1; transform: none; }
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

        .modal-btn--cancel:hover {
            background: var(--gray-200, #e2e8ed);
        }

        .modal-btn--confirm {
            background: var(--teal-500, #00b5a5);
            color: #fff;
            box-shadow: 0 4px 14px rgba(0, 181, 165, .3);
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
            <button class="sidebar-toggle-btn" id="sidebarToggle" title="Навигация по страницам">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <h1 class="test-topbar__title">{{ $test->title }}</h1>

            @if ($test->description)
                <span class="test-topbar__desc">{{ Str::limit($test->description, 60) }}</span>
            @endif

            @if ($test->time_limit > 0)
                <div class="timer-pill" id="timerPill">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                    </svg>
                    <span class="timer-pill__value" id="timer">{{ floor($test->time_limit) }}:00</span>
                </div>
            @endif
        </header>

        {{-- ─────────── BODY ─────────── --}}
        <div class="test-body">

            <div class="sidebar-overlay" id="sidebarOverlay"></div>

            {{-- ─────────── SIDEBAR ─────────── --}}
            <aside class="test-sidebar" id="testSidebar">
                <div class="sidebar-inner">

                    {{-- Progress --}}
                    <div>
                        <div class="sidebar-section-label">Прогресс</div>
                        <div class="sidebar-progress">
                            <div class="sidebar-progress__nums">
                                <span>Страница: <strong>{{ $pageIndex }}</strong></span>
                                <span>Всего: <strong>{{ $totalPages }}</strong></span>
                            </div>
                            <div class="sidebar-progress__bar">
                                <div class="sidebar-progress__fill"
                                    style="width: {{ round(($pageIndex / $totalPages) * 100) }}%"></div>
                            </div>
                            <div class="sidebar-progress__label">
                                @if ($pageIndex === $totalPages)
                                    Последняя страница
                                @else
                                    Осталось страниц: {{ $totalPages - $pageIndex }}
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Page navigation --}}
                    <div>
                        <div class="sidebar-section-label">Страницы</div>
                        <div class="page-nav-list">
                            @for ($p = 1; $p <= $totalPages; $p++)
                                <a href="{{ route('tests.attempt.page', [$test->id, $p]) }}"
                                   class="page-nav-item {{ $p === $pageIndex ? 'active' : '' }}">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2" style="flex-shrink:0">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Страница {{ $p }}
                                </a>
                            @endfor
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div>
                        <div class="sidebar-section-label">Обозначения</div>
                        <div class="sidebar-legend">
                            <div class="legend-item">
                                <div class="legend-dot legend-dot--current"></div>
                                <span>Текущая страница</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot legend-dot--other"></div>
                                <span>Другие страницы</span>
                            </div>
                        </div>
                    </div>

                    {{-- Submit (only on last page) --}}
                    @if ((int)$pageIndex === (int)$totalPages)
                    <button type="button" class="sidebar-submit-btn" id="sidebarSubmitBtn">
                        <span>Завершить</span>
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                    @endif

                </div>
            </aside>

            {{-- ─────────── MAIN ─────────── --}}
            <main class="test-main">
                <form action="{{ route('tests.result', $test->id) }}" method="POST" id="testForm">
                    @csrf

                    @foreach ($questions as $indexOnPage => $question)
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
                                        $selectedAttr = $savedValue == $option['id'] ? 'selected' : '';
                                        $selectHTML .= '<option value="' . $option['id'] . '" ' . $selectedAttr . '>' . htmlspecialchars($option['text'], ENT_QUOTES, 'UTF-8') . '</option>';
                                    }
                                    $selectHTML .= '</select>';

                                    $questionDisplay = str_replace('{' . $blankId . '}', $selectHTML, $questionDisplay);
                                }
                            }
                        @endphp

                        <div class="question-card">
                            <div class="question-card__header">
                                <div class="question-badge">{{ $globalIndexMap[$question->id] ?? ($indexOnPage + 1) }}</div>
                                <p class="question-text">{!! $questionDisplay !!}</p>
                            </div>

                            <div class="question-card__body">

                                @if ($question->question_type === 'short_answer')
                                    @php
                                        $savedText = '';
                                        if (isset($savedAnswers[$question->id]) && is_string($savedAnswers[$question->id])) {
                                            $savedText = $savedAnswers[$question->id];
                                        }
                                    @endphp
                                    <div class="short-answer-wrap">
                                        <label>Ваш ответ</label>
                                        <input type="text"
                                            name="text_answers[{{ $question->id }}]"
                                            class="short-answer-input text-answer-input"
                                            placeholder="Введите ответ..."
                                            value="{{ $savedText }}"
                                            data-question-id="{{ $question->id }}">
                                    </div>

                                @elseif ($question->question_type === 'rich_text_answer')
                                    @php
                                        $savedRichText = '';
                                        if (isset($savedAnswers[$question->id]) && is_string($savedAnswers[$question->id])) {
                                            $savedRichText = $savedAnswers[$question->id];
                                        }
                                    @endphp
                                    <div class="rich-text-wrap">
                                        <label>Развёрнутый ответ</label>
                                        <input type="hidden"
                                            id="rich_text_answer_{{ $question->id }}"
                                            name="rich_text_answers[{{ $question->id }}]"
                                            value="{{ $savedRichText }}">
                                        <trix-editor
                                            input="rich_text_answer_{{ $question->id }}"
                                            data-question-id="{{ $question->id }}"
                                            class="rich-text-answer-input"></trix-editor>
                                    </div>

                                @elseif ($question->question_type === 'fill_in_dropdown')
                                    {{-- dropdowns are already rendered inline in question text above --}}

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
                                                    {{ $isChecked ? 'checked' : '' }}>
                                                <span>{{ $option->option_text }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif

                            </div>
                        </div>
                    @endforeach

                    {{-- Page navigation buttons --}}
                    <div class="question-nav">
                        @if ($pageIndex > 1)
                            <a href="{{ route('tests.attempt.page', [$test->id, $pageIndex - 1]) }}" class="nav-btn">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                                Предыдущая
                            </a>
                        @else
                            <span></span>
                        @endif

                        <span style="font-size:13px;color:var(--color-text-muted);">
                            Страница <strong>{{ $pageIndex }}</strong> из <strong>{{ $totalPages }}</strong>
                        </span>

                        @if ((int)$pageIndex < (int)$totalPages)
                            <a href="{{ route('tests.attempt.page', [$test->id, $pageIndex + 1]) }}" class="nav-btn nav-btn--primary">
                                <span>Следующая</span>
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @else
                            <button type="button" class="nav-btn nav-btn--primary" id="pageSubmitBtn">
                                <span>Завершить</span>
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        @endif
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
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
            </div>
            <h3>Завершить тест?</h3>
            <p>После отправки вы не сможете изменить ответы. Убедитесь, что ответили на все вопросы.</p>
            <div class="modal-actions">
                <button class="modal-btn modal-btn--cancel" id="modalCancelBtn">Вернуться</button>
                <button class="modal-btn modal-btn--confirm" id="modalConfirmBtn">Завершить</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

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

            // ── Answer saving: radio/checkbox ──
            document.querySelectorAll('.answer-input').forEach(input => {
                input.addEventListener('change', async function () {
                    try {
                        const questionId = this.dataset.questionId;
                        let optionIds;
                        if (this.type === 'radio') {
                            optionIds = [this.value];
                        } else {
                            const checked = document.querySelectorAll(`input[name="answers[${questionId}][]"]:checked`);
                            optionIds = Array.from(checked).map(cb => cb.value);
                        }
                        await fetch(`/tests/{{ $test->id }}/save-answer`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ question_id: questionId, option_id: optionIds })
                        });
                    } catch (e) { console.error(e); }
                });
            });

            // ── Answer saving: short text ──
            document.querySelectorAll('.text-answer-input').forEach(input => {
                input.addEventListener('change', async function () {
                    try {
                        await fetch(`/tests/{{ $test->id }}/save-answer`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ question_id: this.dataset.questionId, answer_text: this.value })
                        });
                    } catch (e) { console.error(e); }
                });
            });

            // ── Answer saving: rich text ──
            document.querySelectorAll('.rich-text-answer-input').forEach(editor => {
                editor.addEventListener('trix-change', async function () {
                    try {
                        const inputId = this.getAttribute('input');
                        const val = document.getElementById(inputId).value;
                        await fetch(`/tests/{{ $test->id }}/save-answer`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ question_id: this.dataset.questionId, rich_text_answer: val })
                        });
                    } catch (e) { console.error(e); }
                });
            });

            // ── Answer saving: fill in dropdown ──
            document.querySelectorAll('.fill-in-dropdown-select-inline').forEach(select => {
                select.addEventListener('change', async function () {
                    try {
                        const questionId = this.dataset.questionId;
                        const filledAnswers = {};
                        document.querySelectorAll(`.fill-in-dropdown-select-inline[data-question-id="${questionId}"]`)
                            .forEach(sel => {
                                if (sel.value) filledAnswers[sel.dataset.blankId] = parseInt(sel.value);
                            });
                        await fetch(`/tests/{{ $test->id }}/save-answer`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ question_id: questionId, fill_in_dropdown_answers: filledAnswers })
                        });
                    } catch (e) { console.error(e); }
                });
            });

            // Block file attachments in Trix
            document.addEventListener('trix-file-accept', e => e.preventDefault());
            document.addEventListener('trix-attachment-add', e => { if (e.attachment) e.attachment.remove(); });

            // ── Submit modal (last page) ──
            const sidebarSubmitBtn = document.getElementById('sidebarSubmitBtn');
            const pageSubmitBtn = document.getElementById('pageSubmitBtn');
            const confirmModal = document.getElementById('confirmModal');

            if (sidebarSubmitBtn) {
                sidebarSubmitBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    openModal();
                });
            }

            if (pageSubmitBtn) {
                pageSubmitBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    openModal();
                });
            }

            if (confirmModal) {
                confirmModal.addEventListener('click', function (e) {
                    if (e.target === this) closeModal();
                });

                // Предотвращаем клик внутри modal-box от закрытия модали
                const modalBox = confirmModal.querySelector('.modal-box');
                if (modalBox) {
                    modalBox.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                }

                // Обработчики кнопок в модале
                const modalCancelBtn = document.getElementById('modalCancelBtn');
                const modalConfirmBtn = document.getElementById('modalConfirmBtn');

                if (modalCancelBtn) {
                    modalCancelBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        closeModal();
                    });
                }

                if (modalConfirmBtn) {
                    modalConfirmBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const form = document.getElementById('testForm');
                        if (form) {
                            form.submit();
                        } else {
                            console.error('✗ Форма testForm не найдена');
                        }
                    });
                }
            }
        });

        function openModal() {
            const modal = document.getElementById('confirmModal');
            if (modal) {
                modal.classList.add('active');
                console.log('✓ Модал открыт');
            } else {
                console.error('✗ Элемент confirmModal не найден');
            }
        }

        function closeModal() {
            const modal = document.getElementById('confirmModal');
            if (modal) {
                modal.classList.remove('active');
                console.log('✓ Модал закрыт');
            }
        }

        // ── Timer ──
        @if ($test->time_limit > 0)
            const testId = document.querySelector('meta[name="test-id"]')?.content;
            const serverTimeMeta = parseInt(document.querySelector('meta[name="server-time"]')?.content || 0);
            const testStartTimeMeta = parseInt(document.querySelector('meta[name="test-start-time"]')?.content || 0);
            const clientTimeAtLoad = Date.now() / 1000;
            const timerEl = document.getElementById('timer');
            const timerPill = document.getElementById('timerPill');
            const timeLimitSeconds = {{ $test->time_limit }} * 60;
            let serverTimeOffset = 0;
            let timerInterval;

            function updateTimer() {
                const clientNow = Date.now() / 1000;
                const timeSinceLoad = clientNow - clientTimeAtLoad;
                const currentServerTime = serverTimeMeta + timeSinceLoad + serverTimeOffset;
                const elapsedSeconds = Math.round(currentServerTime - testStartTimeMeta);
                const timeLeftSeconds = Math.max(0, timeLimitSeconds - elapsedSeconds);
                const m = Math.floor(timeLeftSeconds / 60);
                const s = timeLeftSeconds % 60;
                timerEl.textContent = `${m}:${String(s).padStart(2, '0')}`;
                if (timeLeftSeconds <= 60) timerPill.classList.add('urgent');
                if (timeLeftSeconds <= 0) {
                    timerEl.textContent = '00:00';
                    clearInterval(timerInterval);
                    const form = document.querySelector('form[action*="/result"]');
                    if (form) form.submit();
                }
            }

            async function syncWithServer() {
                try {
                    const response = await fetch(`/tests/${testId}/timer-sync`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json'
                        }
                    });
                    if (response.ok) {
                        const data = await response.json();
                        const clientNow = Date.now() / 1000;
                        const calculatedServerTime = serverTimeMeta + (clientNow - clientTimeAtLoad);
                        serverTimeOffset = data.server_time - calculatedServerTime;
                    }
                } catch (e) { console.error(e); }
            }

            updateTimer();
            timerInterval = setInterval(updateTimer, 1000);
            setInterval(syncWithServer, 30000);
        @endif
    </script>
@endsection
