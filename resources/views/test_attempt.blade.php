{{-- test_attempt.blade.php --}}

@extends('layout')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="server-time" content="{{ $serverTime }}">
    <meta name="test-start-time" content="{{ $activeAttempt->started_at->timestamp * 1000 }}">
    <meta name="test-id" content="{{ $test->id }}">
    <meta name="total-questions" content="{{ $totalQuestions }}">
    <meta name="initial-question-index" content="{{ $lastQuestionIndex ?? 0 }}">

    <link rel="stylesheet" href="{{ asset('css/trix.min.css') }}">
    <script src="{{ asset('js/trix.min.js') }}"></script>
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
            margin: 0;
            padding: 0;
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

            0%,
            100% {
                border-color: #ef5350;
            }

            50% {
                border-color: #c62828;
            }
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

        /* Question grid */
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
            box-shadow: 0 2px 8px rgba(0, 181, 165, .35);
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
            background: rgba(255, 204, 0, .7);
        }

        /* Progress */
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

        /* Submit btn */
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

        .sidebar-submit-btn:disabled {
            opacity: .5;
            cursor: not-allowed;
            transform: none;
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

        .legend-dot--empty {
            background: var(--gray-200, #e2e8ed);
        }

        .legend-dot--answered {
            background: var(--teal-400, #26c6b8);
        }

        .legend-dot--active {
            background: var(--teal-500, #00b5a5);
        }

        /* ── MAIN ── */
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
            animation: slideIn .22s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        /* Skeleton loader */
        .question-skeleton {
            background: var(--color-surface, #fff);
            border: 1px solid var(--color-border, #e2e8ed);
            border-radius: var(--r-xl, 20px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, .06);
            overflow: hidden;
            display: none;
        }

        .question-skeleton.visible {
            display: block;
        }

        .skeleton-header {
            padding: 1.5rem 1.75rem 1.25rem;
            border-bottom: 1px solid var(--color-border, #e2e8ed);
            background: var(--gray-50, #f8fafb);
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .skeleton-badge {
            width: 36px;
            height: 36px;
            border-radius: var(--r-md, 12px);
            background: var(--gray-200, #e2e8ed);
            flex-shrink: 0;
            animation: shimmer 1.2s ease infinite;
        }

        .skeleton-lines {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding-top: 6px;
        }

        .skeleton-line {
            height: 14px;
            border-radius: 6px;
            background: var(--gray-200, #e2e8ed);
            animation: shimmer 1.2s ease infinite;
        }

        .skeleton-line:nth-child(1) {
            width: 85%;
        }

        .skeleton-line:nth-child(2) {
            width: 60%;
        }

        .skeleton-body {
            padding: 1.5rem 1.75rem;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .skeleton-option {
            height: 48px;
            border-radius: var(--r-lg, 16px);
            background: var(--gray-100, #f0f3f5);
            animation: shimmer 1.2s ease infinite;
        }

        @keyframes shimmer {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .5;
            }
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
            text-align: left;
        }

        .clearCurrentBtn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: var(--r-md, 12px);
            border: 1.5px solid var(--color-border, #e2e8ed);
            background: var(--color-surface, #fff);
            color: var(--gray-500, #6b7a89);
            cursor: pointer;
            transition: .2s ease;
            flex-shrink: 0;
            padding: 0;
        }

        .clearCurrentBtn:hover {
            border-color: #ef5350;
            color: #c62828;
            background: #ffebee;
        }

        .question-card__body {
            padding: 1.5rem 1.75rem;
        }

        /* Options */
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

        /* Short answer */
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

        /* Rich text */
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

        /* Fill dropdown */
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

        /* Nav buttons */
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
            box-shadow: 0 2px 10px rgba(0, 181, 165, .25);
        }

        .nav-btn--primary:hover:not(:disabled) {
            background: var(--teal-600, #009e90);
            border-color: var(--teal-600, #009e90);
            color: #fff;
        }

        /* Mobile overlay */
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

        /* Modals */
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
            from {
                opacity: 0;
                transform: scale(.95);
            }

            to {
                opacity: 1;
                transform: none;
            }
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

        /* Scrollbar */
        .test-sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .test-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .test-sidebar::-webkit-scrollbar-thumb {
            background: var(--gray-200, #e2e8ed);
            border-radius: 2px;
        }
    </style>
@endsection

@section('content')
    <div class="test-shell">

        {{-- ─────────── TOPBAR ─────────── --}}
        <header class="test-topbar">
            <button class="sidebar-toggle-btn" id="sidebarToggle" title="Навигация по вопросам">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <h1 class="test-topbar__title">{{ $test->title }}</h1>
            @if ($test->description)
                <span class="test-topbar__desc">{{ Str::limit($test->description, 60) }}</span>
            @endif
            @if ($test->time_limit > 0)
                <div class="timer-pill" id="timerPill">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                    </svg>
                    @php
                    $timerM = floor($initialTimeRemaining / 60);
                    $timerS = $initialTimeRemaining % 60;
                @endphp
                <span class="timer-pill__value" id="timer">{{ $timerM }}:{{ str_pad($timerS, 2, '0', STR_PAD_LEFT) }}</span>
                </div>
            @endif
        </header>

        {{-- ─────────── BODY ─────────── --}}
        <div class="test-body">
            <div class="sidebar-overlay" id="sidebarOverlay"></div>

            {{-- ─────────── SIDEBAR ─────────── --}}
            <aside class="test-sidebar" id="testSidebar">
                <div class="sidebar-inner">

                    <div>
                        <div class="sidebar-section-label">Прогресс</div>
                        <div class="sidebar-progress">
                            <div class="sidebar-progress__nums">
                                <span>Отвечено: <strong id="answeredCount">{{ $initialAnsweredCount }}</strong></span>
                                <span>Всего: <strong>{{ $totalQuestions }}</strong></span>
                            </div>
                            <div class="sidebar-progress__bar">
                                <div class="sidebar-progress__fill" id="progressFill"
                                    style="width: {{ $totalQuestions > 0 ? round(($initialAnsweredCount / $totalQuestions) * 100) : 0 }}%">
                                </div>
                            </div>
                            <div class="sidebar-progress__label" id="progressLabel">
                                @if ($initialAnsweredCount === $totalQuestions && $totalQuestions > 0)
                                    Все вопросы отвечены ✓
                                @else
                                    Осталось: {{ $totalQuestions - $initialAnsweredCount }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="sidebar-section-label">Вопросы</div>
                        <div class="q-nav-grid" id="qNavGrid">
                            @for ($i = 0; $i < $totalQuestions; $i++)
                                <button
                                    class="q-nav-btn
                                    {{ $i === ($lastQuestionIndex ?? 0) ? 'active' : '' }}
                                    {{ in_array($i, $answeredIndices) ? 'answered' : '' }}"
                                    data-q-index="{{ $i }}"
                                    title="Вопрос {{ $i + 1 }}">{{ $i + 1 }}</button>
                            @endfor
                        </div>
                    </div>

                    <div>
                        <div class="sidebar-section-label">Обозначения</div>
                        <div class="sidebar-legend">
                            <div class="legend-item">
                                <div class="legend-dot legend-dot--empty"></div><span>Не отвечено</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot legend-dot--answered"></div><span>Отвечено</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot legend-dot--active"></div><span>Текущий вопрос</span>
                            </div>
                        </div>
                    </div>

                    <button class="sidebar-submit-btn" id="sidebarSubmitBtn" type="button">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Завершить тест
                    </button>
                </div>
            </aside>

            {{-- ─────────── MAIN ─────────── --}}
            <main class="test-main">

                {{-- Skeleton shown during AJAX loads --}}
                <div class="question-skeleton" id="questionSkeleton">
                    <div class="skeleton-header">
                        <div class="skeleton-badge"></div>
                        <div class="skeleton-lines">
                            <div class="skeleton-line"></div>
                            <div class="skeleton-line"></div>
                        </div>
                    </div>
                    <div class="skeleton-body">
                        <div class="skeleton-option"></div>
                        <div class="skeleton-option"></div>
                        <div class="skeleton-option"></div>
                    </div>
                </div>

                {{-- Question container — filled via AJAX --}}
                <div id="questionContainer"></div>

                {{-- Hidden form for final submission --}}
                <form action="{{ route('tests.result', $test->id) }}" method="POST" id="testForm"
                    style="display:none;">
                    @csrf
                </form>

                {{-- Navigation --}}
                <div class="question-nav">
                    <button type="button" class="nav-btn" id="prevBtn" disabled>
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        Предыдущий
                    </button>
                    <span style="font-size:13px;color:var(--color-text-muted);">
                        Вопрос <strong id="qCounterCurrent">1</strong> из <strong>{{ $totalQuestions }}</strong>
                    </span>
                    <button type="button" class="nav-btn nav-btn--primary" id="nextBtn">
                        <span id="nextBtnText">Следующий</span>
                        <svg id="nextBtnArrow" width="16" height="16" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </main>
        </div>
    </div>

    {{-- ─────────── CONFIRM MODAL ─────────── --}}
    <div class="modal-backdrop" id="confirmModal">
        <div class="modal-box">
            <div class="modal-icon">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="#e65100"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
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
                <button class="modal-btn modal-btn--cancel" id="modalCancelBtn">Вернуться</button>
                <button class="modal-btn modal-btn--confirm" id="modalConfirmBtn">Завершить</button>
            </div>
        </div>
    </div>

    {{-- ─────────── CLEAR MODAL ─────────── --}}
    <div class="modal-backdrop" id="clearAnswerModal">
        <div class="modal-box">
            <div class="modal-icon">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="#e65100"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3>Очистить ответ?</h3>
            <p>Вы уверены, что хотите удалить ответ на текущий вопрос? Это действие нельзя отменить.</p>
            <div class="modal-actions">
                <button class="modal-btn modal-btn--cancel" id="clearCancelBtn">Отмена</button>
                <button class="modal-btn modal-btn--confirm" id="clearConfirmBtn">Очистить</button>
            </div>
        </div>
    </div>

    <script>
        (function() {
            'use strict';

            /* ── Constants from server ── */
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;
            const TEST_ID = parseInt(document.querySelector('meta[name="test-id"]').content);
            const TOTAL = parseInt(document.querySelector('meta[name="total-questions"]').content);
            const SERVER_TIME = parseInt(document.querySelector('meta[name="server-time"]').content); // seconds
            const START_TIME = parseInt(document.querySelector('meta[name="test-start-time"]').content) /
                1000; // convert ms → seconds
            const INITIAL_IDX = parseInt(document.querySelector('meta[name="initial-question-index"]').content) || 0;

            /* ── State ── */
            let currentQ = INITIAL_IDX;
            let isLoading = false;
            let inputsDisabled = false;

            /* Answered set — initialised from server-rendered sidebar buttons */
            const answeredSet = new Set(
                Array.from(document.querySelectorAll('.q-nav-btn.answered'))
                .map(b => parseInt(b.dataset.qIndex))
            );

            /* ── DOM refs ── */
            const sidebar = document.getElementById('testSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleBtn = document.getElementById('sidebarToggle');
            const qNavGrid = document.getElementById('qNavGrid');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const nextBtnText = document.getElementById('nextBtnText');
            const nextBtnArrow = document.getElementById('nextBtnArrow');
            const qCounter = document.getElementById('qCounterCurrent');
            const answeredCount = document.getElementById('answeredCount');
            const progressFill = document.getElementById('progressFill');
            const progressLabel = document.getElementById('progressLabel');
            const questionContainer = document.getElementById('questionContainer');
            const questionSkeleton = document.getElementById('questionSkeleton');
            const confirmModal = document.getElementById('confirmModal');
            const clearModal = document.getElementById('clearAnswerModal');

            /* ── Sidebar toggle ── */
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
                if (sidebarOpen && window.innerWidth <= 768) overlay.classList.add('active');
                else overlay.classList.remove('active');
            });
            overlay.addEventListener('click', () => {
                sidebarOpen = false;
                applySidebarState();
                overlay.classList.remove('active');
            });

            /* ── Progress UI ── */
            function updateProgress() {
                const count = answeredSet.size;
                answeredCount.textContent = count;
                const pct = TOTAL > 0 ? Math.round((count / TOTAL) * 100) : 0;
                progressFill.style.width = pct + '%';
                progressLabel.textContent = count === TOTAL ?
                    'Все вопросы отвечены ✓' :
                    `Осталось: ${TOTAL - count}`;
            }

            function renderNavBtns() {
                document.querySelectorAll('.q-nav-btn').forEach((btn, i) => {
                    btn.classList.toggle('answered', answeredSet.has(i));
                    btn.classList.toggle('active', i === currentQ);
                });
            }

            function updateNavControls() {
                qCounter.textContent = currentQ + 1;
                prevBtn.disabled = currentQ === 0;
                const isLast = currentQ === TOTAL - 1;
                nextBtnText.textContent = isLast ? 'Завершить' : 'Следующий';
                nextBtnArrow.style.display = isLast ? 'none' : 'inline';
            }

            /* ── Load question via AJAX ── */
            async function loadQuestion(index) {
                if (isLoading) return;
                if (index < 0 || index >= TOTAL) return;

                isLoading = true;

                // Show skeleton, hide old card
                questionContainer.innerHTML = '';
                questionSkeleton.classList.add('visible');

                try {
                    const res = await fetch(`/tests/${TEST_ID}/question/${index}`, {
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) throw new Error('Failed to load question');

                    const data = await res.json();

                    questionSkeleton.classList.remove('visible');
                    questionContainer.innerHTML = data.html;

                    // Re-init Trix editors that appeared in the injected HTML
                    // (Trix auto-inits on connectedCallback, nothing extra needed)

                    currentQ = index;
                    updateNavControls();
                    renderNavBtns();
                    attachAnswerListeners();

                    // Pre-close mobile sidebar
                    if (window.innerWidth <= 768 && sidebarOpen) {
                        sidebarOpen = false;
                        applySidebarState();
                        overlay.classList.remove('active');
                    }

                    // Save progress
                    fetch(`/tests/${TEST_ID}/save-progress`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF
                        },
                        body: JSON.stringify({
                            question_index: index
                        })
                    }).catch(() => {});

                } catch (e) {
                    console.error(e);
                    questionSkeleton.classList.remove('visible');
                    questionContainer.innerHTML =
                        '<p style="padding:2rem;color:#c62828;">Не удалось загрузить вопрос. Попробуйте ещё раз.</p>';
                } finally {
                    isLoading = false;
                }
            }

            /* ── Answer listeners (re-attached on each AJAX load) ── */
            function attachAnswerListeners() {
                if (inputsDisabled) {
                    questionContainer.querySelectorAll('input, select, trix-editor, button').forEach(el => {
                        el.disabled = true;
                    });
                    return;
                }

                // Radio / Checkbox
                questionContainer.querySelectorAll('.answer-input').forEach(input => {
                    input.addEventListener('change', async function() {
                        answeredSet.add(currentQ);
                        updateProgress();
                        renderNavBtns();
                        try {
                            const questionId = this.dataset.questionId;
                            let optionIds;
                            if (this.type === 'radio') {
                                optionIds = this.value;
                            } else {
                                const checked = questionContainer.querySelectorAll(
                                    `input[name="answers[${questionId}][]"]:checked`);
                                optionIds = Array.from(checked).map(cb => cb.value);
                            }
                            await fetch(`/tests/${TEST_ID}/save-answer`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': CSRF,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    question_id: questionId,
                                    option_id: optionIds
                                })
                            });
                        } catch (e) {
                            console.error(e);
                        }
                    });
                });

                // Short text
                questionContainer.querySelectorAll('.text-answer-input').forEach(input => {
                    input.addEventListener('input', function() {
                        if (this.value.trim()) {
                            answeredSet.add(currentQ);
                            updateProgress();
                            renderNavBtns();
                        }
                    });
                    input.addEventListener('change', async function() {
                        try {
                            await fetch(`/tests/${TEST_ID}/save-answer`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': CSRF,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    question_id: this.dataset.questionId,
                                    answer_text: this.value
                                })
                            });
                        } catch (e) {
                            console.error(e);
                        }
                    });
                });

                // Rich text
                questionContainer.querySelectorAll('.rich-text-answer-input').forEach(editor => {
                    editor.addEventListener('trix-change', async function() {
                        const inputId = this.getAttribute('input');
                        const val = document.getElementById(inputId)?.value || '';
                        if (val && val !== '<div></div>') {
                            answeredSet.add(currentQ);
                            updateProgress();
                            renderNavBtns();
                        }
                        try {
                            await fetch(`/tests/${TEST_ID}/save-answer`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': CSRF,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    question_id: this.dataset.questionId,
                                    rich_text_answer: val
                                })
                            });
                        } catch (e) {
                            console.error(e);
                        }
                    });
                });

                // Fill dropdown
                questionContainer.querySelectorAll('.fill-in-dropdown-select-inline').forEach(select => {
                    select.addEventListener('change', async function() {
                        answeredSet.add(currentQ);
                        updateProgress();
                        renderNavBtns();
                        try {
                            const questionId = this.dataset.questionId;
                            const filledAnswers = {};
                            questionContainer.querySelectorAll(
                                    `.fill-in-dropdown-select-inline[data-question-id="${questionId}"]`
                                )
                                .forEach(sel => {
                                    if (sel.value) filledAnswers[sel.dataset.blankId] =
                                        parseInt(sel.value);
                                });
                            await fetch(`/tests/${TEST_ID}/save-answer`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': CSRF,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    question_id: questionId,
                                    fill_in_dropdown_answers: filledAnswers
                                })
                            });
                        } catch (e) {
                            console.error(e);
                        }
                    });
                });

                // Clear button
                questionContainer.querySelectorAll('.clearCurrentBtn').forEach(btn => {
                    btn.addEventListener('click', () => clearModal.classList.add('active'));
                });

                // Block Trix file uploads
                document.addEventListener('trix-file-accept', e => e.preventDefault(), {
                    once: false
                });
                document.addEventListener('trix-attachment-add', e => {
                    if (e.attachment) e.attachment.remove();
                }, {
                    once: false
                });
            }

            /* ── Sidebar question nav ── */
            qNavGrid.addEventListener('click', e => {
                const btn = e.target.closest('.q-nav-btn');
                if (btn) loadQuestion(parseInt(btn.dataset.qIndex));
            });

            /* ── Prev / Next ── */
            prevBtn.addEventListener('click', () => {
                if (currentQ > 0) loadQuestion(currentQ - 1);
            });
            nextBtn.addEventListener('click', () => {
                if (currentQ === TOTAL - 1) openModal();
                else loadQuestion(currentQ + 1);
            });

            /* ── Submit modal ── */
            function openModal() {
                document.getElementById('modalAnswered').textContent = answeredSet.size;
                document.getElementById('modalUnanswered').textContent = TOTAL - answeredSet.size;
                confirmModal.classList.add('active');
            }
            confirmModal.addEventListener('click', e => {
                if (e.target === confirmModal) confirmModal.classList.remove('active');
            });
            confirmModal.querySelector('.modal-box').addEventListener('click', e => e.stopPropagation());
            document.getElementById('modalCancelBtn').addEventListener('click', () => confirmModal.classList.remove(
                'active'));
            document.getElementById('modalConfirmBtn').addEventListener('click', () => {
                document.getElementById('testForm').submit();
            });
            document.getElementById('sidebarSubmitBtn').addEventListener('click', openModal);

            /* ── Clear modal ── */
            clearModal.addEventListener('click', e => {
                if (e.target === clearModal) clearModal.classList.remove('active');
            });
            clearModal.querySelector('.modal-box').addEventListener('click', e => e.stopPropagation());
            document.getElementById('clearCancelBtn').addEventListener('click', () => clearModal.classList.remove(
                'active'));

            document.getElementById('clearConfirmBtn').addEventListener('click', async () => {
                clearModal.classList.remove('active');

                // Find question id from current card
                const questionId = questionContainer.querySelector('[data-question-id]')?.dataset
                    .questionId;
                if (!questionId) return;

                // Clear inputs
                questionContainer.querySelectorAll('.answer-input').forEach(i => i.checked = false);
                const textInput = questionContainer.querySelector('.text-answer-input');
                if (textInput) textInput.value = '';
                const richEditor = questionContainer.querySelector('.rich-text-answer-input');
                if (richEditor) {
                    const hiddenId = richEditor.getAttribute('input');
                    const hidden = document.getElementById(hiddenId);
                    if (hidden) hidden.value = '';
                    richEditor.editor.setSelectedRange([0, richEditor.editor.getDocument().getLength()]);
                    richEditor.editor.deleteInDirection('forward');
                }
                questionContainer.querySelectorAll('.fill-in-dropdown-select-inline').forEach(s => s.value =
                    '');

                answeredSet.delete(currentQ);
                updateProgress();
                renderNavBtns();

                try {
                    await fetch(`/tests/${TEST_ID}/clear-answer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            question_id: questionId
                        })
                    });
                } catch (e) {
                    console.error(e);
                }
            });

            /* ── Timer ── */
            @if ($test->time_limit > 0)
                const clientTimeAtLoad = Date.now() / 1000; // ← верни эту строку
                const timerEl = document.getElementById('timer');
                const timerPill = document.getElementById('timerPill');
                const timeLimitSeconds = {{ $test->time_limit }} * 60;
                let serverTimeOffset = 0;
                let timerInterval;
                let timerTickCount = 0;

                console.log('[TIMER] clientTimeAtLoad:', clientTimeAtLoad);
                console.log('[TIMER] timeLimitSeconds:', timeLimitSeconds);
                console.log('[TIMER] HTML placeholder textContent:', timerEl?.textContent);

                function disableTestInputs() {
                    inputsDisabled = true;
                    if (questionContainer) {
                        questionContainer.querySelectorAll('input, select, trix-editor, button').forEach(el => el
                            .disabled = true);
                    }
                    prevBtn.disabled = true;
                    nextBtn.disabled = true;
                    document.getElementById('sidebarSubmitBtn').disabled = true;
                }

                function updateTimer() {
                    const clientNow = Date.now() / 1000;
                    const timeSinceLoad = clientNow - clientTimeAtLoad;
                    const currentServerTime = SERVER_TIME + timeSinceLoad + serverTimeOffset;
                    const elapsedSeconds = Math.round(currentServerTime - START_TIME);
                    const timeLeftSeconds = Math.max(0, timeLimitSeconds - elapsedSeconds);
                    const m = Math.floor(timeLeftSeconds / 60);
                    const s = timeLeftSeconds % 60;

                    timerTickCount++;
                    if (timerTickCount <= 3 || timerTickCount % 10 === 0) {
                        console.log('[TIMER] tick #' + timerTickCount, {
                            clientNow,
                            timeSinceLoad,
                            currentServerTime,
                            elapsedSeconds,
                            timeLeftSeconds,
                            display: `${m}:${String(s).padStart(2, '0')}`
                        });
                    }

                    timerEl.textContent = `${m}:${String(s).padStart(2, '0')}`;
                    if (timeLeftSeconds <= 60) timerPill.classList.add('urgent');
                    if (timeLeftSeconds <= 0) {
                        timerEl.textContent = '00:00';
                        clearInterval(timerInterval);
                        disableTestInputs();
                        document.getElementById('testForm').submit();
                    }
                }

                async function syncWithServer() {
                    try {
                        const response = await fetch(`/tests/${TEST_ID}/timer-sync`, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                'Accept': 'application/json'
                            }
                        });
                        console.log('[TIMER] sync response status:', response.status);
                        if (response.ok) {
                            const data = await response.json();
                            const clientNow = Date.now() / 1000;
                            const calculatedServerTime = SERVER_TIME + (clientNow - clientTimeAtLoad);
                            serverTimeOffset = data.server_time - calculatedServerTime;
                            console.log('[TIMER] sync success, offset:', serverTimeOffset);
                        } else {
                            const errText = await response.text();
                            console.log('[TIMER] sync failed:', response.status, errText);
                        }
                    } catch (e) {
                        console.error('[TIMER] sync error:', e);
                    }
                }

                updateTimer();
                timerInterval = setInterval(updateTimer, 1000);
                setInterval(syncWithServer, 30000);
            @endif

            /* ── Bootstrap: load the initial question ── */
            updateProgress();
            renderNavBtns();
            updateNavControls();
            loadQuestion(INITIAL_IDX);
        })();
    </script>
@endsection
