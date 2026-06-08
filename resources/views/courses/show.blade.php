<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} - Roodle</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">

    <link rel="stylesheet" href="{{ asset('css/trix.min.css') }}">
    <script src="{{ asset('js/trix.min.js') }}"></script>
    @livewireStyles
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

        [x-cloak] {
            display: none !important;
        }

        @keyframes modal-in {
            from {
                opacity: 0;
                transform: scale(.97) translateY(8px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes modal-out {
            from {
                opacity: 1;
                transform: none;
            }

            to {
                opacity: 0;
                transform: scale(.97) translateY(8px);
            }
        }

        @keyframes overlay-in {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes overlay-out {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        .modal-alpine-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .modal-alpine-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(17, 23, 32, 0.45);
            backdrop-filter: blur(2px);
        }

        .modal-alpine-box {
            position: relative;
            z-index: 1;
            background: var(--color-surface);
            border-radius: var(--r-xl);
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 360px;
            font-family: var(--font-body);
        }
    </style>
</head>

<body>
    @include('components.menu')


    <!-- MAIN -->
    <main class="main">
        <div class="courses-header">
            <div>
                <h1 class="section-title">{{ $course->title }}</h1>
            </div>
        </div>

        <livewire:course-manager :course="$course" />
    </main>
</body>

</html>
