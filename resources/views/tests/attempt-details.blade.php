@extends('layout')

@section('head')
<link rel="stylesheet" href="{{ asset('css/trix.min.css') }}">
<script src="{{ asset('js/trix.min.js') }}"></script>
    <style>
        body { background: var(--color-bg); }

        /* ── FORM CARD SECTIONS (like test_create_form) ── */
        .form-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--r-xl);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
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

        /* ── SCORE HERO (student self-view) ── */
        .score-hero {
            text-align: center;
            padding: 0.5rem 0 0.25rem;
        }

        .score-hero__value {
            font-family: var(--font-display);
            font-size: 64px;
            font-weight: 800;
            letter-spacing: -3px;
            line-height: 1;
            margin-bottom: 4px;
        }

        .score-hero__value.score-high {
            color: var(--green-600);
        }

        .score-hero__value.score-lime {
            color: #76c92e;
        }

        .score-hero__label.score-lime {
            color: #76c92e;
        }

        .score-hero__value.score-mid {
            color: #e65100;
        }

        .score-hero__value.score-low {
            color: #c62828;
        }

        .score-hero__label {
            font-size: 15px;
            font-weight: 600;
            color: var(--color-text-secondary);
            margin-bottom: 12px;
        }

        .score-hero__meta {
            font-size: 13px;
            color: var(--color-text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .score-hero__meta-dot {
            color: var(--gray-300);
        }

        /* ── STATS ROW (admin view) ── */
        .admin-stats {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .admin-stats__score {
            font-family: var(--font-display);
            font-size: 48px;
            font-weight: 800;
            letter-spacing: -2px;
            line-height: 1;
            flex-shrink: 0;
            min-width: 80px;
        }

        .admin-stats__score.score-high {
            color: var(--green-600);
        }

        .admin-stats__score.score-lime {
            color: #76c92e;
        }

        .admin-stats__score.score-mid {
            color: #e65100;
        }

        .admin-stats__score.score-low {
            color: #c62828;
        }

        .admin-stats__body {
            flex: 1;
            min-width: 0;
        }

        .admin-stats__row {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            font-size: 13px;
            color: var(--color-text-secondary);
            line-height: 1.5;
        }

        .admin-stats__row + .admin-stats__row {
            margin-top: 4px;
        }

        .admin-stats__label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--color-text-muted);
        }

        .admin-stats__dot {
            color: var(--gray-300);
            margin: 0 2px;
        }

        .admin-stats__value {
            font-weight: 600;
            color: var(--gray-800);
        }

        .admin-stats__course {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 1px 8px;
            background: var(--teal-50);
            border-radius: var(--r-full);
            font-size: 12px;
            font-weight: 600;
            color: var(--teal-700);
        }

        /* ── QUESTION CARD ── */
        .q-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--r-xl);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
            overflow: hidden;
            margin-bottom: 1rem;
            transition: box-shadow var(--transition);
        }

        .q-card:hover {
            box-shadow: var(--shadow-md);
        }

        .q-card__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--color-border);
            background: var(--gray-50);
        }

        .q-card__header-left {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            flex: 1;
            min-width: 0;
        }

        .q-badge {
            width: 32px;
            height: 32px;
            border-radius: var(--r-md);
            background: var(--teal-50);
            color: var(--teal-600);
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .q-card__meta {
            flex: 1;
            min-width: 0;
        }

        .q-card__number {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--color-text-muted);
            margin-bottom: 3px;
        }

        .q-card__text {
            font-size: 15px;
            font-weight: 600;
            color: var(--gray-800);
            line-height: 1.5;
        }

        .q-type-chip {
            display: inline-flex;
            align-items: center;
            margin-top: 6px;
            padding: 3px 10px;
            border-radius: var(--r-full);
            font-size: 11px;
            font-weight: 700;
            background: var(--sky-50);
            color: var(--sky-700);
        }

        /* ── STATUS BADGE ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 14px;
            border-radius: var(--r-full);
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .status-badge.correct {
            background: var(--green-50);
            color: var(--green-600);
            border: 1.5px solid var(--green-100);
        }

        .status-badge.incorrect {
            background: #ffebee;
            color: #c62828;
            border: 1.5px solid #ffcdd2;
        }

        .status-badge.pending {
            background: #fff8e1;
            color: #e65100;
            border: 1.5px solid #ffecb3;
        }

        .status-badge.empty {
            background: var(--gray-100);
            color: var(--gray-500);
            border: 1.5px solid var(--gray-200);
        }

        .q-card__body {
            padding: 1.25rem 1.5rem;
        }

        .answer-section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--color-text-muted);
            margin-bottom: 8px;
        }

        /* ── TEXT ANSWERS ── */
        .text-answer-box {
            padding: 13px 16px;
            border: 1.5px solid var(--color-border);
            border-radius: var(--r-lg);
            background: var(--gray-50);
            font-size: 14.5px;
            color: var(--gray-700);
            line-height: 1.6;
        }

        .text-answer-box.correct {
            background: var(--green-50);
            border-color: var(--green-400);
            color: #1b5e20;
        }

        .text-answer-box.incorrect {
            background: #ffebee;
            border-color: #ef9a9a;
            color: #b71c1c;
        }

        .rich-text-answer-box {
            padding: 13px 16px;
            border: 1.5px solid var(--color-border);
            border-radius: var(--r-lg);
            background: var(--color-surface);
            font-size: 14.5px;
            color: var(--gray-700);
            line-height: 1.7;
        }

        .rich-text-answer-box.correct {
            background: var(--green-50);
            border-color: var(--green-400);
        }

        .rich-text-answer-box.incorrect {
            background: #ffebee;
            border-color: #ef9a9a;
        }

        .no-answer-box {
            padding: 13px 16px;
            background: var(--gray-100);
            border-radius: var(--r-lg);
            color: var(--color-text-muted);
            font-style: italic;
            font-size: 14px;
        }

        /* ── CORRECT ANSWERS HINT ── */
        .correct-answers-panel {
            margin-top: 12px;
            padding: 12px 14px;
            background: var(--teal-50);
            border-left: 3px solid var(--teal-400);
            border-radius: 0 var(--r-md) var(--r-md) 0;
        }

        .correct-answers-panel__title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--teal-700);
            margin-bottom: 6px;
        }

        .correct-answers-panel p {
            font-size: 13.5px;
            color: var(--teal-800);
            line-height: 1.5;
        }

        /* ── OPTION ITEMS ── */
        .option-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 11px 14px;
            border: 1.5px solid var(--color-border);
            border-radius: var(--r-lg);
            background: var(--color-surface);
            margin-bottom: 8px;
            transition: var(--transition);
        }

        .option-item:last-child {
            margin-bottom: 0;
        }

        .option-item.correct {
            background: var(--green-50);
            border-color: var(--green-400);
        }

        .option-item.selected-correct {
            background: #c8e6c9;
            border-color: #388e3c;
        }

        .option-item.selected-incorrect {
            background: #ffebee;
            border-color: #ef9a9a;
        }

        .option-item__check {
            flex-shrink: 0;
            margin-top: 1px;
        }

        .option-item__text {
            flex: 1;
            font-size: 14px;
            color: var(--gray-700);
            line-height: 1.5;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        .micro-badge {
            display: inline-flex;
            padding: 2px 8px;
            border-radius: var(--r-full);
            font-size: 11px;
            font-weight: 700;
        }

        .micro-badge.user {
            background: #fff3cd;
            color: #856404;
        }

        .micro-badge.should {
            background: var(--green-50);
            color: var(--green-600);
            border: 1px solid var(--green-100);
        }

        /* ── TEACHER GRADING ── */
        .grading-panel {
            margin-top: 14px;
            padding: 14px;
            background: var(--gray-50);
            border: 1.5px solid var(--color-border);
            border-radius: var(--r-lg);
        }

        .grading-panel__title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--color-text-muted);
            margin-bottom: 10px;
        }

        .grading-radios {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .grade-radio-label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: var(--r-full);
            border: 1.5px solid var(--color-border);
            background: var(--color-surface);
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-text-secondary);
            transition: var(--transition);
        }

        .grade-radio-label:hover {
            border-color: var(--teal-300);
            color: var(--teal-700);
            background: var(--teal-50);
        }

        .grade-radio-label input[type="radio"] {
            accent-color: var(--teal-500);
        }

        .grade-radio-label input[type="radio"]:checked+span {
            color: var(--teal-700);
        }

        .grade-radio-label:has(input:checked) {
            border-color: var(--teal-400);
            background: var(--teal-50);
            color: var(--teal-700);
        }

        /* ── SUBMIT BUTTON (clean, like test_create_form btn-primary) ── */
        .submit-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            background: var(--teal-500);
            color: #fff;
            border: none;
            border-radius: var(--r-full);
            font-size: 14px;
            font-weight: 600;
            font-family: var(--font-body);
            cursor: pointer;
            transition: var(--transition);
        }

        .submit-btn:hover {
            background: var(--teal-600);
            transform: translateY(-1px);
        }

        html.dark .option-item.selected-correct {
            background: rgba(16, 185, 129, 0.2);
            border-color: var(--green-500);
        }

        html.dark .option-item.selected-incorrect {
            background: rgba(244, 63, 94, 0.2);
            border-color: var(--red-400);
        }

        html.dark .option-item.correct {
            background: rgba(16, 185, 129, 0.15);
            border-color: var(--green-500);
        }

        html.dark .text-answer-box.correct,
        html.dark .rich-text-answer-box.correct {
            background: rgba(16, 185, 129, 0.15);
            border-color: var(--green-500);
            color: var(--green-400);
        }

        html.dark .text-answer-box.incorrect,
        html.dark .rich-text-answer-box.incorrect {
            background: rgba(244, 63, 94, 0.15);
            border-color: var(--red-400);
            color: var(--red-400);
        }

        html.dark .status-badge.correct {
            background: rgba(16, 185, 129, 0.15);
            color: var(--green-400);
            border-color: rgba(16, 185, 129, 0.3);
        }

        html.dark .status-badge.incorrect {
            background: rgba(244, 63, 94, 0.15);
            color: var(--red-400);
            border-color: rgba(244, 63, 94, 0.3);
        }

        html.dark .status-badge.pending {
            background: rgba(251, 191, 36, 0.15);
            color: #fbbf24;
            border-color: rgba(251, 191, 36, 0.3);
        }

        html.dark .status-badge.empty {
            background: var(--color-surface-2);
            color: var(--color-text-muted);
            border-color: var(--color-border);
        }

        html.dark .correct-answers-panel {
            background: rgba(16, 185, 129, 0.1);
            border-left-color: var(--green-500);
        }

        html.dark .correct-answers-panel__title {
            color: var(--green-400);
        }

        html.dark .correct-answers-panel p {
            color: var(--green-300);
        }

        html.dark .micro-badge.user {
            background: rgba(251, 191, 36, 0.15);
            color: #fbbf24;
        }

        html.dark .micro-badge.should {
            background: rgba(16, 185, 129, 0.15);
            color: var(--green-400);
            border-color: rgba(16, 185, 129, 0.3);
        }

        @media (max-width: 640px) {
            .q-card__header {
                flex-direction: column;
            }

            .status-badge {
                align-self: flex-start;
            }

            .admin-stats {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .admin-stats__score {
                font-size: 36px;
            }

            .score-hero__value {
                font-size: 48px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $isSelfView = auth()->id() === $user->id;
    @endphp

    <div class="layout">
        <aside class="sidebar">
            <p class="sidebar-section-title">Навигация</p>
            <a href="{{ route('tests.view', $test) }}" class="sidebar-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                К тесту
            </a>
            @if($course)
                <a href="{{ route('courses.show', $course) }}" class="sidebar-link">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
                    {{ $course->title }}
                </a>
            @endif
            <a href="{{ route('home') }}" class="sidebar-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Все курсы
            </a>
        </aside>

        <main class="main">
            {{-- Breadcrumb --}}
            <nav style="display: flex; align-items: center; gap: 8px; margin-bottom: 1.75rem; font-size: 13px; color: var(--color-text-muted);">
                <a href="{{ route('home') }}" style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">Курсы</a>
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                @if(optional($course)->title)
                    <a href="{{ route('courses.show', $course) }}" style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">{{ $course->title }}</a>
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                @endif
                <a href="{{ route('tests.view', $test) }}" style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">{{ $test->title }}</a>
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                <span style="color: var(--gray-600); font-weight: 500;">Попытка #{{ $attempt->attempt_number }}</span>
            </nav>

            {{-- Page header --}}
            <div class="page-header">
                <h1 class="page-header__title">Результаты теста: {{ $test->title }}</h1>
            </div>

            {{-- Form card --}}
            <div class="form-card" style="max-width: 900px;">

                {{-- Section: score / info --}}
                <div class="form-section">
                    @if ($isSelfView)
                        @php
                            $correctAnswers = collect($questionDetails)->where('is_correct', true)->count();
                            $totalQuestions = count($questionDetails);
                            $pct = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
                            $grade = 2 + $pct * 3 / 100;
                            $roundedGrade = ceil($grade);
                            if ($roundedGrade >= 5) {
                                $fiveLabel = 'Отлично';
                                $scoreClass = 'score-lime';
                            } elseif ($roundedGrade >= 4) {
                                $fiveLabel = 'Хорошо';
                                $scoreClass = 'score-high';
                            } elseif ($roundedGrade >= 3) {
                                $fiveLabel = 'Удовлетворительно';
                                $scoreClass = 'score-mid';
                            } else {
                                $fiveLabel = 'Неудовлетворительно';
                                $scoreClass = 'score-low';
                            }
                        @endphp
                        {{-- Student self-view: big centered score --}}
                        <div class="score-hero">
                            <div class="score-hero__value {{ $scoreClass }}">
                                {{ number_format($grade, 2, ',', ' ') }}
                            </div>
                            <div class="score-hero__label {{ $scoreClass }}">{{ $fiveLabel }}</div>
                            <div style="font-size:14px;color:var(--color-text-muted);margin-top:4px;margin-bottom:8px;">
                                Правильных ответов: {{ $correctAnswers }} из {{ $totalQuestions }}
                            </div>
                            <div class="score-hero__meta">
                                <span>Попытка #{{ $attempt->attempt_number }}</span>
                                <span class="score-hero__meta-dot">·</span>
                                <span>Завершено {{ $attempt->ended_at->format('d.m.Y H:i') }}</span>
                                <span class="score-hero__meta-dot">·</span>
                                <span>{{ \App\Helpers\TimeFormatter::formatMinutes($attempt->started_at->diffInMinutes($attempt->ended_at)) }}</span>
                            </div>
                        </div>
                    @else
                        @php
                            $pct = $attempt->score;
                            if ($pct >= 85) {
                                $adminScoreClass = 'score-lime';
                            } elseif ($pct >= 70) {
                                $adminScoreClass = 'score-high';
                            } elseif ($pct >= 50) {
                                $adminScoreClass = 'score-mid';
                            } else {
                                $adminScoreClass = 'score-low';
                            }
                        @endphp
                        {{-- Admin view: compact stats row --}}
                        <div class="admin-stats">
                            <div class="admin-stats__score {{ $adminScoreClass }}">
                                {{ $attempt->score }}%
                            </div>
                            <div class="admin-stats__body">
                                <div class="admin-stats__row">
                                    <span class="admin-stats__label">Студент</span>
                                    <span class="admin-stats__value">{{ $user->name }}</span>
                                    <span class="admin-stats__dot">·</span>
                                    <span class="admin-stats__label">Попытка</span>
                                    <span class="admin-stats__value">#{{ $attempt->attempt_number }}</span>
                                    <span class="admin-stats__dot">·</span>
                                    <span class="admin-stats__course">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
                                        {{ optional($course)->title ?? 'Без курса' }}
                                    </span>
                                </div>
                                <div class="admin-stats__row">
                                    <span class="admin-stats__label">Начато</span>
                                    <span class="admin-stats__value">{{ $attempt->started_at->format('d.m.Y H:i') }}</span>
                                    <span class="admin-stats__dot">·</span>
                                    <span class="admin-stats__label">Завершено</span>
                                    <span class="admin-stats__value">{{ $attempt->ended_at->format('d.m.Y H:i') }}</span>
                                    <span class="admin-stats__dot">·</span>
                                    <span class="admin-stats__label">Время</span>
                                    <span class="admin-stats__value">{{ \App\Helpers\TimeFormatter::formatMinutes($attempt->started_at->diffInMinutes($attempt->ended_at)) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Section: answers --}}
                @if ($test->is_details_available || !$isSelfView)
                <div class="form-section">
                    <div class="form-section__title">
                        <div class="form-section__title-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        {{ $isSelfView ? 'Ответы' : 'Ответы студента' }}
                    </div>

                    @hasanyrole('admin|teacher')
                        <form method="POST" action="{{ route('test-attempts.grade-rich-text', $attempt) }}">
                            @csrf
                        @endhasanyrole

                        @foreach ($questionDetails as $index => $detail)
                            @php
                                $question = $detail['question'];
                                $isCorrect = $detail['is_correct'];
                                $hasAnswer = $detail['user_answer_text'] || count($detail['user_selected_option_ids']) > 0;
                                $isManuallyGraded = $detail['is_manually_graded'] ?? false;

                                if ($question->question_type === 'rich_text_answer' && $hasAnswer && !$isManuallyGraded) {
                                    $badgeClass = 'pending';
                                    $badgeText = ' Ожидает проверки';
                                } elseif ($isCorrect) {
                                    $badgeClass = 'correct';
                                    $badgeText = ' Правильно';
                                } elseif ($hasAnswer) {
                                    $badgeClass = 'incorrect';
                                    $badgeText = ' Неправильно';
                                } else {
                                    $badgeClass = 'empty';
                                    $badgeText = '— Не ответил';
                                }

                                $typeLabel = match ($question->question_type) {
                                    'single_choice' => 'Один ответ',
                                    'multiple_choice' => 'Несколько ответов',
                                    'rich_text_answer' => 'Развёрнутый ответ',
                                    'fill_in_dropdown' => 'Выпадающий список',
                                    'fill_in_the_blank' => 'Заполнение пропуска',
                                    default => 'Текстовый ответ',
                                };
                            @endphp

                            <div class="q-card">

                                {{-- Card header --}}
                                <div class="q-card__header">
                                    <div class="q-card__header-left">
                                        <div class="q-badge">{{ $index + 1 }}</div>
                                        <div class="q-card__meta">
                                            <div class="q-card__number">Вопрос {{ $index + 1 }}</div>
                                            <div class="q-card__text">{{ strip_tags(preg_replace('/<figcaption[^>]*>.*?<\/figcaption>/si', '', $question->question_text)) }}</div>
                                            @if (!$isSelfView)<span class="q-type-chip">{{ $typeLabel }}</span>@endif
                                        </div>
                                    </div>
                                    <span class="status-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                                </div>

                                {{-- Card body --}}
                                <div class="q-card__body">

                                    @if ($question->question_type === 'short_answer')
                                        <div class="answer-section-label">Ответ студента</div>
                                        @if ($detail['user_answer_text'])
                                            <div class="text-answer-box {{ $isCorrect ? 'correct' : 'incorrect' }}">
                                                {{ $detail['user_answer_text'] }}
                                            </div>
                                        @else
                                            <div class="no-answer-box">Студент не дал ответ</div>
                                        @endif

                                        @if (!$isCorrect)
                                            <div class="correct-answers-panel">
                                                <div class="correct-answers-panel__title">Правильные ответы</div>
                                                @foreach ($question->options->where('is_correct', true) as $option)
                                                    <p>• {{ $option->option_text }}</p>
                                                @endforeach
                                            </div>
                                        @endif
                                    @elseif($question->question_type === 'rich_text_answer')
                                        <div class="answer-section-label">Ответ студента</div>
                                        @if ($detail['user_answer_text'])
                                            <div
                                                class="rich-text-answer-box {{ $isManuallyGraded ? ($isCorrect ? 'correct' : 'incorrect') : '' }}">
                                                {!! $detail['user_answer_text'] !!}
                                            </div>
                                        @else
                                            <div class="no-answer-box">Студент не дал ответ</div>
                                        @endif

                                        @if ($detail['user_answer_text'])
                                            @hasanyrole('admin|teacher')
                                                <div class="grading-panel">
                                                    <div class="grading-panel__title">Оценка учителя</div>
                                                    <div class="grading-radios">
                                                        <label class="grade-radio-label">
                                                            <input type="radio" name="grades[{{ $question->id }}]" value="correct"
                                                                {{ $isManuallyGraded && $isCorrect ? 'checked' : '' }}>
                                                            <span> Засчитать как правильный</span>
                                                        </label>
                                                        <label class="grade-radio-label">
                                                            <input type="radio" name="grades[{{ $question->id }}]" value="incorrect"
                                                                {{ $isManuallyGraded && !$isCorrect ? 'checked' : '' }}>
                                                            <span> Отметить как неправильный</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endhasanyrole
                                        @endif
                                    @elseif(in_array($question->question_type, ['single_choice', 'multiple_choice']))
                                        {{-- single_choice / multiple_choice --}}
                                        <div class="answer-section-label">Варианты ответа</div>
                                        @foreach ($question->options as $option)
                                            @php
                                                $isUserSelected = in_array($option->id, $detail['user_selected_option_ids']);
                                                $isCorrectOption = $option->is_correct;

                                                if ($isUserSelected && $isCorrectOption) {
                                                    $cls = 'selected-correct';
                                                } elseif ($isUserSelected && !$isCorrectOption) {
                                                    $cls = 'selected-incorrect';
                                                } elseif ($isCorrectOption) {
                                                    $cls = 'correct';
                                                } else {
                                                    $cls = '';
                                                }
                                            @endphp
                                            <div class="option-item {{ $cls }}">
                                                <div class="option-item__check">
                                                    <input type="checkbox" disabled {{ $isUserSelected ? 'checked' : '' }}
                                                        style="width:16px;height:16px;accent-color:var(--teal-500);margin-top:2px;">
                                                </div>
                                                <div class="option-item__text">
                                                    {{ $option->option_text }}
                                                    @if ($isUserSelected && !$isSelfView)
                                                        <span class="micro-badge user">Выбран студентом</span>
                                                    @endif
                                                    @if ($isCorrectOption && !$isUserSelected && !$isSelfView)
                                                        <span class="micro-badge should">Должен быть выбран</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @elseif($question->question_type === 'fill_in_dropdown')
                                        <div class="answer-section-label">Ответ студента (выпадающий список)</div>
                                        @php
                                            $byBlank = [];
                                            foreach ($question->options as $opt) {
                                                $decoded = json_decode($opt->option_text, true);
                                                if (!is_array($decoded)) {
                                                    continue;
                                                }
                                                $bid = $decoded['blank_id'];
                                                $byBlank[$bid][] = [
                                                    'id' => $opt->id,
                                                    'text' => $decoded['text'] ?? '',
                                                    'is_correct' => (bool) $opt->is_correct,
                                                    'selected' => in_array($opt->id, $detail['user_selected_option_ids']),
                                                ];
                                            }
                                            ksort($byBlank);
                                        @endphp

                                        @if (empty($detail['user_selected_option_ids']))
                                            <div class="no-answer-box">Студент не дал ответ</div>
                                        @else
                                            @foreach ($byBlank as $blankId => $opts)
                                                @php
                                                    $selected = collect($opts)->firstWhere('selected', true);
                                                    $correct = collect($opts)->firstWhere('is_correct', true);
                                                    $isBlankCorrect = $selected && $selected['is_correct'];
                                                @endphp
                                                <div
                                                    class="option-item {{ $isBlankCorrect ? 'selected-correct' : ($selected ? 'selected-incorrect' : '') }}">
                                                    <div class="option-item__text">
                                                        <span
                                                            style="color:var(--color-text-muted);font-size:12px;font-weight:700;min-width:70px;">
                                                            Пропуск {{ $blankId }}:
                                                        </span>
                                                        @if ($selected)
                                                            <strong>{{ $selected['text'] }}</strong>
                                                            @if (!$isBlankCorrect && $correct)
                                                                <span class="micro-badge should">Правильно:
                                                                    {{ $correct['text'] }}</span>
                                                            @endif
                                                        @else
                                                            <em style="color:var(--color-text-muted)">не выбрано</em>
                                                            @if ($correct)
                                                                <span class="micro-badge should">Правильно:
                                                                    {{ $correct['text'] }}</span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    @elseif($question->question_type === 'fill_in_the_blank')
                                        {{-- fill_in_the_blank --}}
                                        <div class="answer-section-label">Ответ студента (пропуски)</div>
                                        @php
                                            $userBlanks = [];
                                            if ($detail['user_answer_text']) {
                                                $decoded = json_decode($detail['user_answer_text'], true);
                                                if (is_array($decoded)) {
                                                    foreach ($decoded as $b) {
                                                        $userBlanks[$b['blank_id']] = $b['text'] ?? '';
                                                    }
                                                }
                                            }

                                            $correctBlanks = [];
                                            foreach ($question->options->where('is_correct', true) as $opt) {
                                                $decoded = json_decode($opt->option_text, true);
                                                if (is_array($decoded) && isset($decoded['blank_id'])) {
                                                    $correctBlanks[$decoded['blank_id']][] = $decoded['text'] ?? '';
                                                }
                                            }
                                        @endphp

                                        @if (empty($userBlanks))
                                            <div class="no-answer-box">Студент не дал ответ</div>
                                        @else
                                            @foreach ($userBlanks as $blankId => $userText)
                                                @php
                                                    $correct = $correctBlanks[$blankId] ?? [];
                                                    $isBlankCorrect = collect($correct)->contains(
                                                        fn($c) => mb_strtolower(trim($c)) === mb_strtolower(trim($userText)),
                                                    );
                                                @endphp
                                                <div class="option-item {{ $isBlankCorrect ? 'selected-correct' : 'selected-incorrect' }}"
                                                    style="margin-bottom:8px;">
                                                    <div class="option-item__text">
                                                        <span
                                                            style="color:var(--color-text-muted);font-size:12px;font-weight:700;min-width:70px;">
                                                            Пропуск {{ $blankId }}:
                                                        </span>
                                                        <strong>{{ $userText ?: '(пусто)' }}</strong>
                                                        @if (!$isBlankCorrect && count($correct))
                                                            <span class="micro-badge should">
                                                                Правильно: {{ implode(' / ', $correct) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    @else
                                        {{-- fallback --}}
                                        <div class="answer-section-label">Ответ студента</div>
                                        @if ($detail['user_answer_text'])
                                            <div class="text-answer-box">{{ $detail['user_answer_text'] }}</div>
                                        @else
                                            <div class="no-answer-box">Студент не дал ответ</div>
                                        @endif
                                    @endif

                                </div>
                            </div>
                        @endforeach

                        @php
                            $hasRichTextQuestions = collect($questionDetails)->contains(
                                fn($d) => $d['question']->question_type === 'rich_text_answer'
                            );
                        @endphp

                        @hasanyrole('admin|teacher')
                            @if ($hasRichTextQuestions)
                            <div class="grade-submit-wrap" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--color-border);">
                                <button type="submit" class="submit-btn">
                                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Сохранить оценки развёрнутых ответов
                                </button>
                            </div>
                            @endif
                        </form>
                    @endhasanyrole

                </div>
                @endif
            </div>
        </main>
    </div>
@endsection
