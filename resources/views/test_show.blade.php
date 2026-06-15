<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $test->title }}</title>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/trix.min.css') }}">
    <script src="{{ asset('js/trix.min.js') }}"></script>

    <style>
        .tq-wrap {
            max-width: 760px;
            margin: 0 auto;
            padding: 2rem 1.5rem 5rem;
        }

        .tq-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-text-secondary);
            text-decoration: none;
            padding: 5px 12px 5px 8px;
            border-radius: var(--r-full);
            transition: var(--transition);
            margin-bottom: 1.5rem;
        }

        .tq-back:hover {
            background: var(--gray-100);
            color: var(--gray-800);
        }

        .tq-header {
            margin-bottom: 2rem;
        }

        .tq-header__eyebrow {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--teal-600);
            margin-bottom: 6px;
        }

        .tq-header__title {
            font-family: var(--font-display);
            font-size: 28px;
            color: var(--gray-900);
            line-height: 1.2;
            margin-bottom: 6px;
        }

        .tq-header__desc {
            font-size: 14px;
            color: var(--color-text-secondary);
            line-height: 1.6;
        }

        .tq-section {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--r-xl);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .tq-section__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--color-border);
        }

        .tq-section__label {
            font-size: 14px;
            font-weight: 700;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tq-count {
            background: var(--gray-100);
            color: var(--gray-600);
            font-size: 12px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: var(--r-full);
        }

        .tq-add-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            background: var(--teal-500);
            color: #fff;
            border: none;
            border-radius: var(--r-full);
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow-accent);
        }

        .tq-add-btn:hover {
            background: var(--teal-600);
            transform: translateY(-1px);
        }

        .tq-paged-bar {
            background: var(--sky-50);
            border-bottom: 1px solid var(--sky-100);
            padding: 10px 1.5rem;
            font-size: 13px;
            color: var(--sky-700);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tq-list {
            list-style: none;
        }

        .tq-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.9rem 1.5rem;
            border-bottom: 1px solid var(--color-border);
            transition: background var(--transition);
        }

        .tq-row:last-child {
            border-bottom: none;
        }

        .tq-row:hover {
            background: var(--gray-50);
        }

        .tq-row__num {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--gray-100);
            color: var(--gray-500);
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .tq-row__text {
            flex: 1;
            font-size: 14px;
            color: var(--gray-800);
            line-height: 1.4;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .tq-row__type {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: var(--r-full);
            flex-shrink: 0;
        }

        .tq-type--single {
            background: var(--teal-50);
            color: var(--teal-700);
        }

        .tq-type--multiple {
            background: var(--sky-50);
            color: var(--sky-700);
        }

        .tq-type--short {
            background: var(--gray-100);
            color: var(--gray-600);
        }

        .tq-type--rich {
            background: #f3f0ff;
            color: #6d28d9;
        }

        .tq-type--dropdown {
            background: #fff7ed;
            color: #c2410c;
        }

        .tq-row__actions {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
        }

        .tq-icon-btn {
            width: 32px;
            height: 32px;
            border: 1px solid var(--color-border);
            border-radius: var(--r-sm);
            background: transparent;
            color: var(--gray-400);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .tq-icon-btn:hover {
            background: var(--gray-100);
            color: var(--gray-700);
            border-color: var(--gray-300);
        }

        .tq-icon-btn--delete:hover {
            background: #fff0f0;
            color: var(--red-500);
            border-color: #fecaca;
        }

        .tq-icon-btn svg {
            width: 15px;
            height: 15px;
        }

        .tq-page-input {
            width: 52px;
            padding: 4px 6px;
            border: 1px solid var(--color-border);
            border-radius: var(--r-sm);
            font-size: 13px;
            font-family: var(--font-body);
            text-align: center;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .tq-page-input:focus {
            outline: none;
            border-color: var(--teal-400);
            box-shadow: 0 0 0 2px rgba(0, 181, 165, .1);
        }

        .tq-save-pages {
            display: flex;
            justify-content: flex-end;
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--color-border);
        }

        .tq-empty {
            padding: 3rem 1.5rem;
            text-align: center;
            color: var(--color-text-muted);
        }

        .tq-empty svg {
            margin-bottom: 12px;
            opacity: .25;
        }

        .tq-empty p {
            font-size: 14px;
            font-weight: 500;
            color: var(--color-text-secondary);
            margin-bottom: 4px;
        }

        .tq-empty span {
            font-size: 13px;
            color: var(--color-text-muted);
        }

        /* ─── MODALS ─── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(17, 23, 32, 0.45);
            backdrop-filter: blur(2px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal {
            background: var(--color-surface);
            border-radius: var(--r-xl);
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-height: calc(100vh - 3rem);
            overflow-y: auto;
            position: relative;
            animation: modal-in .16s ease;
        }

        @keyframes modal-in {
            from {
                opacity: 0;
                transform: scale(.97) translateY(6px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .modal--sm {
            max-width: 360px;
        }

        .modal--md {
            max-width: 540px;
        }

        .modal--lg {
            max-width: 680px;
        }

        .modal__head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.25rem 1.5rem 1rem;
            border-bottom: 1px solid var(--color-border);
        }

        .modal__title {
            font-size: 16px;
            font-weight: 700;
            color: var(--gray-800);
            line-height: 1.3;
        }

        .modal__subtitle {
            font-size: 13px;
            color: var(--color-text-muted);
            margin-top: 2px;
        }

        .modal__close {
            width: 28px;
            height: 28px;
            border: none;
            background: var(--gray-100);
            color: var(--gray-500);
            border-radius: var(--r-sm);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: var(--transition);
            font-size: 17px;
            line-height: 1;
        }

        .modal__close:hover {
            background: var(--gray-200);
            color: var(--gray-800);
        }

        .modal__body {
            padding: 1.25rem 1.5rem;
        }

        .modal__footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        /* ─── ADD CHOICE ─── */
        .add-choice-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .add-choice-card {
            padding: 1.25rem;
            border: 1.5px solid var(--color-border);
            border-radius: var(--r-lg);
            cursor: pointer;
            transition: var(--transition);
            background: transparent;
            font-family: var(--font-body);
            text-align: left;
        }

        .add-choice-card:hover {
            border-color: var(--teal-400);
            background: var(--teal-50);
        }

        .add-choice-card__icon {
            width: 36px;
            height: 36px;
            border-radius: var(--r-md);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .aci--new {
            background: var(--teal-50);
            color: var(--teal-600);
        }

        .aci--bank {
            background: var(--sky-50);
            color: var(--sky-600);
        }

        .add-choice-card__title {
            font-size: 14px;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 3px;
        }

        .add-choice-card__desc {
            font-size: 12px;
            color: var(--color-text-muted);
            line-height: 1.4;
        }

        /* ─── DETAIL MODAL ─── */
        .detail-qtext {
            font-size: 15px;
            font-weight: 600;
            color: var(--gray-900);
            line-height: 1.55;
            margin-bottom: 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--color-border);
        }

        .detail-section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--color-text-muted);
            margin-bottom: 8px;
        }

        .detail-opts {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .detail-opt {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: var(--r-md);
            font-size: 14px;
            color: var(--gray-700);
            background: var(--gray-50);
            border: 1px solid var(--color-border);
        }

        .detail-opt.correct {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #15803d;
            font-weight: 600;
        }

        .detail-opt__dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 1.5px solid var(--gray-300);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .detail-opt.correct .detail-opt__dot {
            background: #22c55e;
            border-color: #22c55e;
        }

        .detail-correct-text {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: var(--r-md);
            padding: 9px 13px;
            font-size: 14px;
            color: #15803d;
            font-weight: 600;
        }

        .detail-blank {
            background: var(--gray-50);
            border: 1px solid var(--color-border);
            border-radius: var(--r-md);
            padding: 10px 14px;
            margin-bottom: 8px;
        }

        .detail-blank__title {
            font-size: 12px;
            font-weight: 700;
            color: var(--teal-700);
            margin-bottom: 7px;
        }

        /* ─── CREATE FORM ─── */
        .ff {
            margin-bottom: 1.1rem;
        }

        .ff label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--gray-500);
            margin-bottom: 5px;
        }

        .ff input[type="text"],
        .ff select,
        .ff textarea {
            width: 100%;
            padding: 8px 11px;
            border: 1px solid var(--color-border);
            border-radius: var(--r-md);
            font-size: 14px;
            font-family: var(--font-body);
            color: var(--color-text-primary);
            background: var(--color-surface);
            transition: var(--transition);
        }

        .ff input[type="text"]:focus,
        .ff select:focus,
        .ff textarea:focus {
            outline: none;
            border-color: var(--teal-400);
            box-shadow: 0 0 0 3px rgba(0, 181, 165, .1);
        }

        .ff select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7a89' stroke-width='2.5'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 30px;
            cursor: pointer;
        }

        trix-editor {
            border: 1px solid var(--color-border) !important;
            border-radius: 0 0 var(--r-md) var(--r-md) !important;
            min-height: 80px;
            font-size: 14px;
            font-family: var(--font-body) !important;
            padding: 9px 12px !important;
        }

        trix-editor:focus {
            outline: none !important;
            border-color: var(--teal-400) !important;
        }

        trix-toolbar {
            border: 1px solid var(--color-border);
            border-bottom: none;
            border-radius: var(--r-md) var(--r-md) 0 0;
            background: var(--gray-50);
            padding: 5px 8px;
        }

        .opts-form {
            display: flex;
            flex-direction: column;
            gap: 7px;
            margin-bottom: 8px;
        }

        .opt-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .opt-row input[type="text"] {
            flex: 1;
            padding: 7px 10px;
            border: 1px solid var(--color-border);
            border-radius: var(--r-md);
            font-size: 13px;
            font-family: var(--font-body);
            transition: var(--transition);
        }

        .opt-row input[type="text"]:focus {
            outline: none;
            border-color: var(--teal-400);
            box-shadow: 0 0 0 2px rgba(0, 181, 165, .1);
        }

        .opt-row input[type="radio"],
        .opt-row input[type="checkbox"] {
            accent-color: var(--teal-500);
            width: 15px;
            height: 15px;
            flex-shrink: 0;
            cursor: pointer;
        }

        .add-opt-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border: 1px dashed var(--color-border-2);
            border-radius: var(--r-md);
            background: none;
            font-size: 12px;
            font-weight: 600;
            color: var(--color-text-muted);
            font-family: var(--font-body);
            cursor: pointer;
            transition: var(--transition);
        }

        .add-opt-btn:hover {
            border-color: var(--teal-400);
            color: var(--teal-700);
            background: var(--teal-50);
        }

        .cbrow {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            color: var(--color-text-secondary);
            cursor: pointer;
            margin-top: 10px;
        }

        .cbrow input {
            accent-color: var(--teal-500);
            width: 14px;
            height: 14px;
        }

        .ddb-block {
            background: var(--gray-50);
            border: 1px solid var(--color-border);
            border-radius: var(--r-md);
            padding: 10px 12px;
            margin-bottom: 8px;
        }

        .ddb-block>label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--teal-700);
            margin-bottom: 7px;
        }

        .ddo-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
        }

        .ddo-row input[type="text"] {
            flex: 1;
            padding: 6px 10px;
            border: 1px solid var(--color-border);
            border-radius: var(--r-sm);
            font-size: 13px;
            font-family: var(--font-body);
        }

        .ddo-row input[type="radio"] {
            accent-color: var(--teal-500);
            width: 14px;
            height: 14px;
        }

        /* ─── BUTTONS ─── */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            background: var(--teal-500);
            color: #fff;
            border: none;
            border-radius: var(--r-full);
            font-family: var(--font-body);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow-accent);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            background: var(--teal-600);
        }

        .btn-primary:disabled {
            opacity: .6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-danger {
            background: var(--red-500);
            box-shadow: none;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            background: transparent;
            color: var(--color-text-secondary);
            border: 1px solid var(--color-border-2);
            border-radius: var(--r-full);
            font-family: var(--font-body);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-ghost:hover {
            background: var(--gray-100);
            color: var(--gray-800);
        }

        /* ─── ALERTS ─── */
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
            border-radius: var(--r-md);
            padding: 9px 13px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .alert-error {
            background: #fff0f0;
            border: 1px solid #fecaca;
            color: var(--red-500);
            border-radius: var(--r-md);
            padding: 10px 13px;
            font-size: 13px;
            margin-bottom: 1rem;
        }

        .alert-error strong {
            display: block;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .alert-error ul {
            margin: 0;
            padding-left: 16px;
        }

        .modal::-webkit-scrollbar {
            width: 5px;
        }

        .modal::-webkit-scrollbar-thumb {
            background: var(--gray-200);
            border-radius: 3px;
        }

        body {
    margin: 0;
    padding: 0;
}
    </style>
</head>

<body>
    @include("components.menu")
    <div class="tq-wrap">

        @if($test->course)
            <a href="{{ route('courses.show', $test->course) }}" class="tq-back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <path d="M19 12H5M12 19l-7-7 7-7" />
                </svg>
                К курсу
            </a>
        @endif

        <div class="tq-header">
            <div class="tq-header__eyebrow">Тест</div>
            <div class="tq-header__title">{{ $test->title }}</div>
            @if ($test->description)
                <div class="tq-header__desc">{{ $test->description }}</div>
            @endif
        </div>

        {{-- Paged mode needs a wrapping form --}}
        @if ($test->display_mode === 'paged')
            <form action="{{ route('tests.update_layout', $test) }}" method="POST" id="paged-form">
                @csrf
                @method('PUT')
        @endif

        <div class="tq-section">
            <div class="tq-section__head">
                <div class="tq-section__label">
                    Вопросы
                    <span class="tq-count">{{ $test->questions->count() }}</span>
                </div>
                <button type="button" class="tq-add-btn" onclick="openModal('modal-choice')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    Добавить вопрос
                </button>
            </div>

            @if ($test->display_mode === 'paged')
                <div class="tq-paged-bar">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 16v-4M12 8h.01" />
                    </svg>
                    Страничный режим — укажите номер страницы для каждого вопроса
                </div>
            @endif

            <ul class="tq-list">
                @forelse($test->questions as $question)
                    @php
                        $tMap = [
                            'single_choice' => ['Один ответ', 'tq-type--single'],
                            'multiple_choice' => ['Несколько', 'tq-type--multiple'],
                            'short_answer' => ['Текст', 'tq-type--short'],
                            'rich_text_answer' => ['Развёрнутый', 'tq-type--rich'],
                            'fill_in_dropdown' => ['Пропуски', 'tq-type--dropdown'],
                        ];
                        $ti = $tMap[$question->question_type] ?? [$question->question_type, 'tq-type--short'];
                    @endphp
                    <li class="tq-row">
                        <div class="tq-row__num">{{ $loop->iteration }}</div>
                        <div class="tq-row__text" title="{{ strip_tags($question->question_text) }}">
                            {{ strip_tags($question->question_text) }}
                        </div>
                        <span class="tq-row__type {{ $ti[1] }}">{{ $ti[0] }}</span>

                        @if ($test->display_mode === 'paged')
                            <input type="number" name="pages[{{ $question->id }}]" min="1"
                                value="{{ $question->pivot->page_number ?? 1 }}" class="tq-page-input"
                                title="Номер страницы">
                        @endif

                        <div class="tq-row__actions">
                            <button type="button" class="tq-icon-btn" title="Детали вопроса"
                                onclick="openDetail({{ $question->id }})">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                            <button type="button" class="tq-icon-btn tq-icon-btn--delete" title="Удалить из теста"
                                onclick="askDelete({{ $question->id }})">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6" />
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                    <path d="M10 11v6M14 11v6" />
                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                                </svg>
                            </button>
                        </div>
                    </li>
                @empty
                    <li>
                        <div class="tq-empty">
                            <svg width="44" height="44" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.2">
                                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" />
                                <rect x="9" y="3" width="6" height="4" rx="1" />
                            </svg>
                            <p>Вопросов пока нет</p>
                            <span>Нажмите «Добавить вопрос», чтобы начать</span>
                        </div>
                    </li>
                @endforelse
            </ul>

            @if ($test->display_mode === 'paged' && $test->questions->count() > 0)
                <div class="tq-save-pages">
                    <button type="submit" form="paged-form" class="btn-primary">Сохранить страницы</button>
                </div>
            @endif
        </div>

        @if ($test->display_mode === 'paged')
            </form>
        @endif

    </div>


    {{-- ═══ MODAL: выбор способа ═══ --}}
    <div class="modal-overlay" id="modal-choice" onclick="overlayClick(event,'modal-choice')">
        <div class="modal modal--sm">
            <div class="modal__head">
                <div>
                    <div class="modal__title">Добавить вопрос</div>
                    <div class="modal__subtitle">Выберите способ</div>
                </div>
                <button class="modal__close" onclick="closeModal('modal-choice')">×</button>
            </div>
            <div class="modal__body">
                <div class="add-choice-grid">
                    <button class="add-choice-card" type="button"
                        onclick="closeModal('modal-choice');openModal('modal-create')">
                        <div class="add-choice-card__icon aci--new">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                        </div>
                        <div class="add-choice-card__title">Новый вопрос</div>
                        <div class="add-choice-card__desc">Создать с нуля</div>
                    </button>
                    <button class="add-choice-card" type="button"
                        onclick="closeModal('modal-choice');openModal('modal-bank')">
                        <div class="add-choice-card__icon aci--bank">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                            </svg>
                        </div>
                        <div class="add-choice-card__title">Из банка</div>
                        <div class="add-choice-card__desc">Готовый вопрос</div>
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- ═══ MODAL: создать новый вопрос ═══ --}}
    <div class="modal-overlay" id="modal-create" onclick="overlayClick(event,'modal-create')">
        <div class="modal modal--lg">
            <div class="modal__head">
                <div>
                    <div class="modal__title">Новый вопрос</div>
                    <div class="modal__subtitle">{{ $test->title }}</div>
                </div>
                <button class="modal__close" onclick="closeModal('modal-create')">×</button>
            </div>
            <form action="/tests/{{ $test->id }}/questions" method="POST" id="question-form">
                @csrf
                <div class="modal__body">
                    {{-- ── AI-генерация ── --}}
                    <div class="ff" style="background:var(--teal-50);border:1px solid var(--teal-100);border-radius:var(--r-md);padding:12px 14px;margin-bottom:1.2rem;">
                        <label style="color:var(--teal-700);">✨ Сгенерировать с помощью AI</label>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <input type="text" id="ai-prompt" placeholder="Например: вопрос про ООП в Python"
                                style="flex:1;padding:8px 11px;border:1px solid var(--teal-200);border-radius:var(--r-md);font-size:14px;font-family:var(--font-body);">
                            <button type="button" id="ai-gen-btn" onclick="generateQuestion()"
                                style="padding:8px 16px;background:var(--teal-500);color:#fff;border:none;border-radius:var(--r-full);font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;font-family:var(--font-body);transition:var(--transition);">
                                Сгенерировать
                            </button>
                        </div>
                        <div id="ai-status" style="font-size:12px;color:var(--teal-700);margin-top:6px;display:none;"></div>
                    </div>
                    @if ($errors->any())
                        <div class="alert-error">
                            <strong>Ошибки:</strong>
                            <ul>
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="ff">
                        <label>Тип вопроса</label>
                        <select name="question_type" id="question_type" required>
                            <option value="single_choice">Один правильный ответ</option>
                            <option value="multiple_choice">Несколько правильных ответов</option>
                            <option value="short_answer">Текстовый ответ</option>
                            <option value="rich_text_answer">Развёрнутый ответ</option>
                            <option value="fill_in_dropdown">Пропуски с выпадающим списком</option>
                        </select>
                    </div>

                    <div class="ff" id="q-text-group">
                        <label>Текст вопроса</label>
                        <input id="question_text" type="hidden" name="question_text">
                        <trix-editor input="question_text"></trix-editor>
                    </div>

                    <div class="ff" id="opts-group">
                        <label>Варианты <span
                                style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--color-text-muted);">(отметьте
                                правильный)</span></label>
                        <div id="opts-container" class="opts-form">
                            <div class="opt-row"><input type="radio" name="correct_option" value="0"><input
                                    type="text" name="options[0]" placeholder="Вариант 1"></div>
                            <div class="opt-row"><input type="radio" name="correct_option" value="1"><input
                                    type="text" name="options[1]" placeholder="Вариант 2"></div>
                        </div>
                        <button type="button" id="add-opt-btn" class="add-opt-btn">+ Добавить вариант</button>
                    </div>

                    <div class="ff" id="text-ans-group" style="display:none;">
                        <label>Правильные ответы</label>
                        <div id="text-ans-container" class="opts-form">
                            <div class="opt-row"><input type="text" name="correct_answers[0]"
                                    placeholder="Правильный ответ" required></div>
                        </div>
                        <button type="button" id="add-ans-btn" class="add-opt-btn">+ Добавить ответ</button>
                        <label class="cbrow">
                            <input type="checkbox" name="case_insensitive" value="1" checked>
                            Игнорировать регистр и пробелы
                        </label>
                        <input type="hidden" name="case_insensitive" value="0">
                    </div>

                    <div class="ff" id="fill-group" style="display:none;">
                        <label>Текст с пропусками</label>
                        <textarea name="fill_text" id="fill_text" rows="3"
                            placeholder="Пример: «Париж — столица {1}, а Лондон — столица {2}»"></textarea>
                        <div id="dropdowns-container" style="margin-top:10px;"></div>
                    </div>
                </div>
                <div class="modal__footer">
                    <button type="button" class="btn-ghost" onclick="closeModal('modal-create')">Отмена</button>
                    <button type="submit" class="btn-primary" id="submit-btn">Создать вопрос</button>
                </div>
            </form>
        </div>
    </div>


    {{-- ═══ MODAL: из банка вопросов ═══ --}}
    <div class="modal-overlay" id="modal-bank" onclick="overlayClick(event,'modal-bank')">
        <div class="modal modal--md">
            <div class="modal__head">
                <div>
                    <div class="modal__title">Банк вопросов</div>
                    <div class="modal__subtitle">Выберите вопрос для добавления</div>
                </div>
                <button class="modal__close" onclick="closeModal('modal-bank')">×</button>
            </div>
            <form action="/tests/{{ $test->id }}/add-from-bank" method="POST">
                @csrf
                <div class="modal__body">
                    <div class="ff">
                        <label>Поиск</label>
                        <input type="text" placeholder="Начните вводить…" oninput="filterBank(this.value)">
                    </div>
                    <div class="ff">
                        <label>Вопросы</label>
                        <select name="question_id" required id="bank-select" size="9"
                            style="height:auto;padding:0;">
                            @foreach ($allQuestions as $q)
                                <option value="{{ $q->id }}"
                                    data-q="{{ strtolower(strip_tags($q->question_text)) }}"
                                    style="padding:8px 10px;border-bottom:1px solid var(--color-border);">
                                    {{ strip_tags($q->question_text) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal__footer">
                    <button type="button" class="btn-ghost" onclick="closeModal('modal-bank')">Отмена</button>
                    <button type="submit" class="btn-primary">Добавить в тест</button>
                </div>
            </form>
        </div>
    </div>


    {{-- ═══ MODAL: детали вопроса ═══ --}}
    <div class="modal-overlay" id="modal-detail" onclick="overlayClick(event,'modal-detail')">
        <div class="modal modal--md">
            <div class="modal__head">
                <div>
                    <div class="modal__title">Детали вопроса</div>
                    <div class="modal__subtitle" id="detail-type"></div>
                </div>
                <button class="modal__close" onclick="closeModal('modal-detail')">×</button>
            </div>
            <div class="modal__body" id="detail-body"></div>
        </div>
    </div>


    {{-- ═══ MODAL: уведомление ═══ --}}
    <div class="modal-overlay" id="modal-notification" onclick="overlayClick(event,'modal-notification')">
        <div class="modal modal--sm" style="text-align:center;padding:2rem 1.5rem;">
            <div style="width:52px;height:52px;border-radius:20px;background:#fff3e0;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="#e65100" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
            </div>
            <h3 style="font-size:18px;font-weight:700;color:var(--gray-800);margin-bottom:.5rem;">Внимание</h3>
            <p style="font-size:14px;color:var(--color-text-secondary);line-height:1.6;margin-bottom:1.5rem;" id="notifMessage"></p>
            <button class="btn-primary" onclick="closeModal('modal-notification')" style="margin:0 auto;">OK</button>
        </div>
    </div>


    {{-- ═══ MODAL: подтверждение удаления ═══ --}}
    <div class="modal-overlay" id="modal-delete" onclick="overlayClick(event,'modal-delete')">
        <div class="modal modal--sm">
            <div class="modal__head">
                <div>
                    <div class="modal__title">Удалить вопрос?</div>
                    <div class="modal__subtitle">Вопрос будет убран из этого теста</div>
                </div>
                <button class="modal__close" onclick="closeModal('modal-delete')">×</button>
            </div>
            <div class="modal__footer">
                <button type="button" class="btn-ghost" onclick="closeModal('modal-delete')">Отмена</button>
                <button type="button" class="btn-primary btn-danger" id="delete-ok-btn">Удалить</button>
            </div>
        </div>
    </div>


    {{-- Hidden delete forms --}}
    @foreach ($test->questions as $q)
        <form id="del-form-{{ $q->id }}"
            action="{{ route('tests.removeQuestion', ['test' => $test->id, 'question' => $q->id]) }}" method="POST"
            style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    {{-- Question data for detail modal --}}
    @php
        $qData = $test->questions->map(function ($q) {
            $opts = [];

            if ($q->question_type === 'fill_in_dropdown') {
                $blanks = [];
                foreach ($q->options as $opt) {
                    $d = json_decode($opt->option_text, true);
                    if (is_array($d) && isset($d['blank_id'], $d['text'])) {
                        $blanks[$d['blank_id']][] = [
                            'text' => $d['text'],
                            'is_correct' => (bool) $opt->is_correct,
                        ];
                    }
                }
                ksort($blanks);
                $opts = $blanks;
            } else {
                foreach ($q->options as $opt) {
                    $opts[] = [
                        'text' => $opt->option_text,
                        'is_correct' => (bool) $opt->is_correct,
                    ];
                }
            }

            return [
                'id' => $q->id,
                'text' => $q->question_text,
                'type' => $q->question_type,
                'options' => $opts,
            ];
        });
    @endphp

    <script>
        const Q_DATA = @json($qData);
    </script>

    <script>

async function generateQuestion() {
    const prompt = document.getElementById('ai-prompt').value.trim();
    if (!prompt) { showNotification('Введите промпт'); return; }

    const btn = document.getElementById('ai-gen-btn');
    const status = document.getElementById('ai-status');
    btn.disabled = true;
    btn.textContent = '…';
    status.style.display = 'block';
    status.textContent = 'Генерирую вопрос…';

    try {
        const res = await fetch("{{ route('questions.generate') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                prompt: prompt,
                question_type: qType.value,
            }),
        });

        const data = await res.json();

        if (!res.ok || data.error) {
            status.textContent = '❌ Ошибка: ' + (data.error || 'неизвестная');
            return;
        }

        // Вставляем текст вопроса в Trix
        const trixEl = document.querySelector('trix-editor');
        if (trixEl) {
            trixEl.editor.loadHTML(data.question_text);
        }

        // Заполняем варианты ответов
        const t = qType.value;
        if (t === 'single_choice' || t === 'multiple_choice') {
            // Очищаем существующие варианты
            optCont.innerHTML = '';
            data.options.forEach((opt, i) => {
                const it = t === 'single_choice' ? 'radio' : 'checkbox';
                const na = t === 'single_choice' ? 'correct_option' : 'correct_options[]';
                const row = document.createElement('div');
                row.className = 'opt-row';
                row.innerHTML = `<input type="${it}" name="${na}" value="${i}" ${opt.is_correct ? 'checked' : ''}>` +
                    `<input type="text" name="options[${i}]" value="${opt.text.replace(/"/g,'&quot;')}" placeholder="Вариант ${i+1}" required>`;
                optCont.appendChild(row);
            });
        } else if (t === 'short_answer') {
            ansCont.innerHTML = '';
            const correct = data.options.filter(o => o.is_correct);
            correct.forEach((opt, i) => {
                const row = document.createElement('div');
                row.className = 'opt-row';
                row.innerHTML = `<input type="text" name="correct_answers[${i}]" value="${opt.text.replace(/"/g,'&quot;')}" placeholder="Правильный ответ" required>`;
                ansCont.appendChild(row);
            });
        }

        status.textContent = '✅ Готово! Проверьте и отредактируйте если нужно.';

    } catch (e) {
        status.textContent = '❌ Ошибка сети: ' + e.message;
    } finally {
        btn.disabled = false;
        btn.textContent = 'Сгенерировать';
    }
}
        /* ── MODAL UTILS ── */
        function openModal(id) {
            document.getElementById(id).classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
            document.body.style.overflow = '';
        }

        function overlayClick(e, id) {
            if (e.target === e.currentTarget) closeModal(id);
        }
        function showNotification(msg) {
            document.getElementById('notifMessage').textContent = msg;
            openModal('modal-notification');
        }
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList
                .remove('open'));
        });

        /* ── DELETE ── */
        let pendingDel = null;

        function askDelete(id) {
            pendingDel = id;
            openModal('modal-delete');
        }
        document.getElementById('delete-ok-btn').onclick = () => {
            if (pendingDel) document.getElementById('del-form-' + pendingDel).submit();
        };

        /* ── DETAIL ── */
        const TYPE_LABELS = {
            single_choice: 'Один правильный ответ',
            multiple_choice: 'Несколько правильных ответов',
            short_answer: 'Текстовый ответ',
            rich_text_answer: 'Развёрнутый ответ',
            fill_in_dropdown: 'Пропуски с выпадающим списком'
        };
        const checkSvg =
            `<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>`;

        function openDetail(id) {
            const q = Q_DATA.find(x => x.id === id);
            if (!q) return;
            document.getElementById('detail-type').textContent = TYPE_LABELS[q.type] || q.type;
            let h = `<div class="detail-qtext">${q.text}</div>`;

            if (q.type === 'fill_in_dropdown') {
                h += `<div class="detail-section-label">Пропуски</div>`;
                Object.entries(q.options).forEach(([bId, opts]) => {
                    h +=
                        `<div class="detail-blank"><div class="detail-blank__title">Пропуск #${bId}</div><ul class="detail-opts">`;
                    opts.forEach(o => {
                        h +=
                            `<li class="detail-opt${o.is_correct?' correct':''}"><div class="detail-opt__dot">${o.is_correct?checkSvg:''}</div>${o.text}</li>`;
                    });
                    h += `</ul></div>`;
                });
            } else if (q.type === 'short_answer') {
                h += `<div class="detail-section-label">Правильные ответы</div>`;
                q.options.filter(o => o.is_correct).forEach(o => {
                    h += `<div class="detail-correct-text">✓ ${o.text}</div>`;
                });
            } else if (q.type === 'rich_text_answer') {
                h +=
                    `<div class="detail-section-label">Проверяется вручную</div><p style="font-size:13px;color:var(--color-text-muted);">Развёрнутые ответы оценивает преподаватель.</p>`;
            } else {
                h += `<div class="detail-section-label">Варианты ответов</div><ul class="detail-opts">`;
                q.options.forEach(o => {
                    h +=
                        `<li class="detail-opt${o.is_correct?' correct':''}"><div class="detail-opt__dot">${o.is_correct?checkSvg:''}</div>${o.text}</li>`;
                });
                h += `</ul>`;
            }
            document.getElementById('detail-body').innerHTML = h;
            openModal('modal-detail');
        }

        /* ── BANK FILTER ── */
        function filterBank(val) {
            const q = val.toLowerCase();
            document.querySelectorAll('#bank-select option').forEach(o => {
                o.style.display = o.dataset.q.includes(q) ? '' : 'none';
            });
        }

        /* ── CREATE FORM: type-driven UI ── */
        const qType = document.getElementById('question_type');
        const optGrp = document.getElementById('opts-group');
        const optCont = document.getElementById('opts-container');
        const ansGrp = document.getElementById('text-ans-group');
        const ansCont = document.getElementById('text-ans-container');
        const fillGrp = document.getElementById('fill-group');
        const qTextGrp = document.getElementById('q-text-group');

        function refreshUI() {
            const t = qType.value;
            const oi = optCont.querySelectorAll('input[type="text"],input[type="radio"],input[type="checkbox"]');
            const ai = ansCont.querySelectorAll('input[type="text"]');

            const show = (el, v) => el.style.display = v ? 'block' : 'none';

            if (t === 'short_answer') {
                show(qTextGrp, 1);
                show(optGrp, 0);
                show(ansGrp, 1);
                show(fillGrp, 0);
                oi.forEach(i => {
                    i.required = false;
                    i.removeAttribute('required');
                });
                ai.forEach(i => i.required = true);
            } else if (t === 'rich_text_answer') {
                show(qTextGrp, 1);
                show(optGrp, 0);
                show(ansGrp, 0);
                show(fillGrp, 0);
                oi.forEach(i => {
                    i.required = false;
                    i.removeAttribute('required');
                });
                ai.forEach(i => {
                    i.required = false;
                    i.removeAttribute('required');
                });
            } else if (t === 'fill_in_dropdown') {
                show(qTextGrp, 0);
                show(optGrp, 0);
                show(ansGrp, 0);
                show(fillGrp, 1);
                optCont.querySelectorAll('input').forEach(i => i.removeAttribute('name'));
                ansCont.querySelectorAll('input[type="text"]').forEach(i => i.removeAttribute('name'));
                oi.forEach(i => {
                    i.required = false;
                    i.removeAttribute('required');
                });
                ai.forEach(i => {
                    i.required = false;
                    i.removeAttribute('required');
                });
                buildDropdownsUI();
            } else {
                show(qTextGrp, 1);
                show(optGrp, 1);
                show(ansGrp, 0);
                show(fillGrp, 0);
                oi.forEach(i => {
                    if (i.type === 'text') i.required = true;
                });
                ai.forEach(i => {
                    i.required = false;
                    i.removeAttribute('required');
                });
                refreshOptInputs();
            }
        }

        function refreshOptInputs() {
            const t = qType.value;
            Array.from(optCont.children).forEach((div, i) => {
                const inp = div.querySelector('input[type="radio"],input[type="checkbox"]');
                if (!inp) return;
                if (t === 'single_choice') {
                    inp.type = 'radio';
                    inp.name = 'correct_option';
                    inp.required = true;
                } else {
                    inp.type = 'checkbox';
                    inp.name = 'correct_options[]';
                    inp.required = false;
                }
            });
        }

        function buildDropdownsUI() {
            const text = document.getElementById('fill_text').value;
            const cont = document.getElementById('dropdowns-container');
            const re = /\{(\d+)\}/g;
            let m;
            let blanks = [];
            while ((m = re.exec(text)) !== null) {
                const i = parseInt(m[1]);
                if (!blanks.includes(i)) blanks.push(i);
            }
            blanks.sort((a, b) => a - b);
            const ex = {};
            cont.querySelectorAll('.ddb-block').forEach(b => {
                const ml = b.querySelector('label')?.textContent.match(/Пропуск #(\d+)/);
                if (ml) ex[ml[1]] = b;
            });
            Object.keys(ex).forEach(i => {
                if (!blanks.includes(parseInt(i))) ex[i].remove();
            });
            blanks.forEach(idx => {
                if (ex[idx]) {
                    cont.appendChild(ex[idx]);
                    return;
                }
                const block = document.createElement('div');
                block.className = 'ddb-block';
                block.innerHTML =
                    `<label>Пропуск #${idx}: варианты (один правильный)</label>
                <div id="ddo-${idx}">
                    <div class="ddo-row"><input type="radio" name="dropdown_correct[${idx}]" value="0" required><input type="text" name="dropdown_options[${idx}][]" placeholder="Вариант 1" required></div>
                    <div class="ddo-row"><input type="radio" name="dropdown_correct[${idx}]" value="1"><input type="text" name="dropdown_options[${idx}][]" placeholder="Вариант 2" required></div>
                </div>
                <button type="button" class="add-opt-btn ddb-add" data-b="${idx}" style="margin-top:4px;">+ Вариант</button>`;
                cont.appendChild(block);
            });
        }

        document.getElementById('fill_text').addEventListener('input', buildDropdownsUI);
        document.getElementById('dropdowns-container').addEventListener('click', e => {
            if (e.target.classList.contains('ddb-add')) {
                const idx = e.target.dataset.b;
                const div = document.getElementById('ddo-' + idx);
                const cnt = div.querySelectorAll('.ddo-row').length;
                const row = document.createElement('div');
                row.className = 'ddo-row';
                row.innerHTML =
                    `<input type="radio" name="dropdown_correct[${idx}]" value="${cnt}"><input type="text" name="dropdown_options[${idx}][]" placeholder="Вариант ${cnt+1}" required>`;
                div.appendChild(row);
            }
        });

        qType.addEventListener('change', refreshUI);
        refreshUI();

        document.getElementById('add-opt-btn').addEventListener('click', () => {
            const idx = optCont.children.length;
            const t = qType.value;
            const it = t === 'single_choice' ? 'radio' : 'checkbox';
            const na = t === 'single_choice' ? 'correct_option' : 'correct_options[]';
            const row = document.createElement('div');
            row.className = 'opt-row';
            row.innerHTML =
                `<input type="${it}" name="${na}" value="${idx}"><input type="text" name="options[${idx}]" placeholder="Вариант ${idx+1}" required>`;
            optCont.appendChild(row);
            if (optGrp.style.display === 'none') row.querySelectorAll('input').forEach(i => i.required = false);
        });

        document.getElementById('add-ans-btn').addEventListener('click', () => {
            const idx = ansCont.children.length;
            const row = document.createElement('div');
            row.className = 'opt-row';
            row.innerHTML =
                `<input type="text" name="correct_answers[${idx}]" placeholder="Правильный ответ ${idx+1}" required>`;
            ansCont.appendChild(row);
            if (ansGrp.style.display === 'none') row.querySelector('input').required = false;
        });

        /* ── IMAGE UPLOAD ── */
        document.addEventListener('trix-attachment-add', e => {
            if (e.attachment.file) upload(e.attachment);
        });

        function upload(att) {
            const fd = new FormData();
            fd.append('file', att.file);
            fetch("{{ route('questions.upload') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: fd,
                    credentials: 'same-origin'
                }).then(r => r.json()).then(d => {
                    if (d.location) att.setAttributes({
                        url: d.location,
                        href: d.location
                    });
                })
                .catch(() => showNotification('Ошибка загрузки изображения'));
        }

        document.getElementById('question-form').addEventListener('submit', function(e) {
            const t = qType.value;

            // Принудительно синхронизируем Trix в hidden input
            const trixEl = document.querySelector('trix-editor');
            if (trixEl) {
                document.getElementById('question_text').value = trixEl.editor.getDocument().toString().trim() ?
                    trixEl.innerHTML :
                    '';
            }

            if (t === 'short_answer' || t === 'rich_text_answer')
                optCont.querySelectorAll('input').forEach(i => i.removeAttribute('name'));
            if (t !== 'short_answer')
                ansCont.querySelectorAll('input[type="text"]').forEach(i => i.removeAttribute('name'));

            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.textContent = 'Создание…';
            // Убираем setTimeout с reload — он мешает получить ошибки валидации
        });

        /* ── AUTO-OPEN on errors ── */
        @if ($errors->any())
            openModal('modal-create');
        @endif
    </script>
</body>

</html>
