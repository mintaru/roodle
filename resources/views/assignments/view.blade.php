<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $assignment->title }}</title>

    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">



    <link rel="stylesheet" href="{{ asset('css/trix.min.css') }}">
    <script src="{{ asset('js/trix.min.js') }}"></script>

    <script defer src="{{ asset('js/alpine.min.js') }}"></script>

    <style>

        .assignment-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 1.5rem;
            align-items: start;
        }

        .assignment-main {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .assignment-sidebar {
            position: sticky;
            top: 90px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .hero-card {
            background: linear-gradient(
                135deg,
                var(--teal-600) 0%,
                var(--sky-600) 100%
            );

            border-radius: var(--r-2xl);

            padding: 2.5rem;

            color: white;

            position: relative;

            overflow: hidden;
        }

        .hero-card::before {
            content: "";

            position: absolute;

            top: -90px;
            right: -90px;

            width: 260px;
            height: 260px;

            border-radius: 999px;

            background: rgba(255,255,255,.08);
        }

        .hero-label {
            position: relative;
            z-index: 1;

            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: .8;
            font-weight: 700;

            margin-bottom: .75rem;
        }

        .hero-title {
            position: relative;
            z-index: 1;

            font-family: var(--font-display);

            font-size: 40px;

            line-height: 1.1;

            margin-bottom: 1rem;
        }

        .hero-meta {
            position: relative;
            z-index: 1;

            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
        }

        .content-card {
            background: var(--color-surface);

            border: 1px solid var(--color-border);

            border-radius: var(--r-xl);

            padding: 1.75rem;

            box-shadow: var(--shadow-sm);
        }

        .content-card__title {
            font-size: 20px;

            font-weight: 700;

            color: var(--gray-800);

            margin-bottom: 1rem;
        }

        .content-text {
            color: var(--color-text-secondary);

            line-height: 1.8;

            font-size: 15px;
        }

        .info-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: .25rem;
        }

        .info-label {
            font-size: 12px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .5px;

            color: var(--color-text-muted);
        }

        .info-value {
            font-size: 14px;

            font-weight: 600;

            color: var(--gray-800);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            width: fit-content;

            padding: 6px 12px;

            border-radius: var(--r-full);

            font-size: 12px;

            font-weight: 700;
        }

        .status-submitted {
            background: var(--green-50);
            color: var(--green-700);
        }

        .status-graded {
            background: var(--sky-50);
            color: var(--sky-700);
        }

        .status-overdue {
            background: #ffebee;
            color: var(--red-500);
        }

        .success-message {
            background: var(--green-50);

            color: var(--green-700);

            border: 1px solid var(--green-100);

            border-radius: var(--r-lg);

            padding: 14px 16px;

            font-size: 14px;

            font-weight: 600;
        }

        .file-list {
            display: flex;
            flex-direction: column;
            gap: .75rem;

            list-style: none;

            padding: 0;
            margin: 0;
        }

        .file-item {
            background: var(--gray-50);

            border: 1px solid var(--color-border);

            border-radius: var(--r-lg);

            padding: 1rem;
        }

        .file-item__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .file-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--gray-800);
        }

        .file-meta {
            font-size: 12px;
            color: var(--color-text-muted);
            margin-top: 4px;
        }

        .audio-player {
            width: 100%;
            margin-top: .75rem;
        }

        .submission-answer {
            background: white;

            border: 1px solid var(--color-border);

            border-radius: var(--r-lg);

            padding: 1rem;

            line-height: 1.7;

            color: var(--gray-700);
        }

        .divider {
            height: 1px;
            background: var(--color-border);
            margin: 1.5rem 0;
        }

        .teacher-comment {
            margin-top: 1rem;

            background: #fff8e1;

            border: 1px solid #ffe082;

            border-radius: var(--r-lg);

            padding: 1rem;

            color: var(--gray-700);

            line-height: 1.6;
        }

        .grade-display {
            font-size: 40px;
            font-weight: 800;
            color: var(--green-600);
            line-height: 1;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;

            margin-bottom: .5rem;

            font-size: 13px;

            font-weight: 700;

            color: var(--gray-700);
        }

        .form-control {
            width: 100%;

            padding: 12px 14px;

            border: 1px solid var(--color-border);

            border-radius: var(--r-lg);

            background: var(--color-surface);

            font-size: 14px;

            font-family: var(--font-body);

            transition: .2s;
        }

        .form-control:focus {
            outline: none;

            border-color: var(--teal-400);

            box-shadow: 0 0 0 3px rgba(0,181,165,.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 160px;
        }

        .submission-table-wrap {
            overflow-x: auto;
        }

        .submission-table {
            width: 100%;
            border-collapse: collapse;
        }

        .submission-table th {
            text-align: left;

            padding: 1rem;

            font-size: 12px;

            text-transform: uppercase;

            letter-spacing: .5px;

            color: var(--color-text-muted);

            border-bottom: 1px solid var(--color-border);
        }

        .submission-table td {
            padding: 1rem;

            border-bottom: 1px solid var(--color-border);

            vertical-align: top;
        }

        .submission-row:hover {
            background: var(--gray-50);
        }

        .submission-details {
            background: var(--gray-50);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            padding: 10px 18px;

            border-radius: var(--r-full);

            font-size: 14px;

            font-weight: 700;

            border: none;

            cursor: pointer;

            text-decoration: none;

            transition: .2s;

            font-family: var(--font-body);
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--teal-500);

            color: white;

            box-shadow: var(--shadow-accent);
        }

        .btn-secondary {
            background: var(--gray-100);

            color: var(--gray-700);

            border: 1px solid var(--color-border);
        }

        .btn-danger {
            background: var(--red-500);
            color: white;
        }

        .btn-sm {
            padding: 8px 14px;
            font-size: 12px;
        }

        .empty-state {
            color: var(--color-text-muted);
            font-size: 14px;
        }

        @media (max-width: 960px) {

            .assignment-layout {
                grid-template-columns: 1fr;
            }

            .assignment-sidebar {
                position: static;
            }

            .hero-card {
                padding: 2rem;
            }

            .hero-title {
                font-size: 30px;
            }
        }

        @media (max-width: 768px) {

            .hero-card {
                padding: 1.5rem;
            }

            .content-card {
                padding: 1.25rem;
            }

            .submission-table {
                min-width: 720px;
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

@include('components.menu')

<div class="layout">

    {{-- Sidebar --}}
    <aside class="sidebar">

        <p class="sidebar-section-title">
            Навигация
        </p>

        <a href="{{ route('courses.show', $course) }}"
           class="sidebar-link">

            <svg width="16"
                 height="16"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">

                <path d="M19 12H5M12 5l-7 7 7 7"/>

            </svg>

            К курсу

        </a>

        <a href="{{ route('home') }}"
           class="sidebar-link">

            <svg width="16"
                 height="16"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">

                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>

            </svg>

            Все курсы

        </a>

        <p class="sidebar-section-title"
           style="margin-top:2rem;">

            Курс

        </p>

        <div style="padding:0 .75rem;">

            <p style="
                font-size:13px;
                font-weight:600;
                color:var(--gray-800);
                line-height:1.4;
            ">
                {{ $course->title }}
            </p>

        </div>

    </aside>

    {{-- Main --}}
    <main class="main">

        {{-- Breadcrumb --}}
        <nav style="
            display:flex;
            align-items:center;
            gap:8px;
            margin-bottom:1.75rem;
            font-size:13px;
            color:var(--color-text-muted);
        ">

            <a href="{{ route('home') }}"
               style="color:var(--color-text-muted); text-decoration:none;"
               onmouseover="this.style.color='var(--teal-600)'"
               onmouseout="this.style.color='var(--color-text-muted)'">

                Курсы

            </a>

            <svg width="14"
                 height="14"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">

                <path d="M9 18l6-6-6-6"/>

            </svg>

            <a href="{{ route('courses.show', $course) }}"
               style="color:var(--color-text-muted); text-decoration:none;"
               onmouseover="this.style.color='var(--teal-600)'"
               onmouseout="this.style.color='var(--color-text-muted)'">

                {{ $course->title }}

            </a>

            <svg width="14"
                 height="14"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">

                <path d="M9 18l6-6-6-6"/>

            </svg>

            <span style="
                color:var(--gray-600);
                font-weight:500;
            ">
                {{ $assignment->title }}
            </span>

        </nav>

        @if(session('success'))

            <div class="success-message"
                 style="margin-bottom:1.5rem;">

                {{ session('success') }}

            </div>

        @endif

        <div class="assignment-layout">

            {{-- Main content --}}
            <div class="assignment-main">

                {{-- Hero --}}
                <section class="hero-card">

                    <div class="hero-label">
                        Задание
                    </div>

                    <h1 class="hero-title">
                        {{ $assignment->title }}
                    </h1>

                    <div class="hero-meta">

                        @if($assignment->isOverdue())

                            <span class="status-badge status-overdue">
                                Срок истёк
                            </span>

                        @endif

                        @if($assignment->due_date)

                            <span class="status-badge status-graded">
                                До {{ $assignment->due_date->format('d.m.Y H:i') }}
                            </span>

                        @endif

                    </div>

                </section>

                {{-- Description --}}
                @if($assignment->description)

                    <div class="content-card">

                        <h2 class="content-card__title">
                            Описание
                        </h2>

                        <div class="content-text">
                            {{ $assignment->description }}
                        </div>

                    </div>

                @endif

                {{-- Instructions --}}
                @if($assignment->instructions)

                    <div class="content-card">

                        <h2 class="content-card__title">
                            Инструкции
                        </h2>

                        <div class="content-text">
                            {!! nl2br(e($assignment->instructions)) !!}
                        </div>

                    </div>

                @endif

                {{-- Files --}}
                @if($assignment->files->count())

                    <div class="content-card">

                        <h2 class="content-card__title">
                             Файлы задания
                        </h2>

                        <ul class="file-list">

                            @foreach($assignment->files as $file)

                                <li class="file-item">

                                    <div class="file-item__top">

                                        <div>

                                            <div class="file-title">
                                                {{ $file->title }}
                                            </div>

                                            <div class="file-meta">
                                                {{ number_format($file->file_size / 1024, 2) }} KB
                                            </div>

                                        </div>

                                        <a href="{{ route('assignments.download-file', [$course, $assignment, $file]) }}"
                                           class="btn btn-secondary btn-sm">

                                            Скачать

                                        </a>

                                    </div>

                                    @if(strtolower(pathinfo($file->file_name ?? $file->title, PATHINFO_EXTENSION)) === 'mp3')

                                        <audio controls
                                               class="audio-player">

                                            <source
                                                src="{{ route('assignments.download-file', [$course, $assignment, $file]) }}"
                                                type="audio/mpeg">

                                        </audio>

                                    @endif

                                </li>

                            @endforeach

                        </ul>

                    </div>

                @endif

            </div>

            {{-- Sidebar --}}
            <aside class="assignment-sidebar">

                <div class="content-card">

                    <h3 class="content-card__title">
                        Информация
                    </h3>

                    <div class="info-list">

                        <div class="info-item">

                            <span class="info-label">
                                Курс
                            </span>

                            <span class="info-value">
                                {{ $course->title }}
                            </span>

                        </div>

                        @if($assignment->due_date)

                            <div class="info-item">

                                <span class="info-label">
                                    Срок сдачи
                                </span>

                                <span class="info-value">
                                    {{ $assignment->due_date->format('d.m.Y H:i') }}
                                </span>

                            </div>

                        @endif

                        <div class="info-item">

                            <span class="info-label">
                                Статус
                            </span>

                            @if($assignment->isOverdue())

                                <span class="status-badge status-overdue">
                                    Просрочено
                                </span>

                            @else

                                <span class="status-badge status-graded">
                                    Активно
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            </aside>

        </div>

    </main>

</div>

<script>
    function toggleSubmissionDetails(submissionId) {

        const details = document.getElementById(
            'submission-details-' + submissionId
        );

        if (
            details.style.display === 'none' ||
            details.style.display === ''
        ) {

            details.style.display = 'table-row';

        } else {

            details.style.display = 'none';
        }
    }
</script>

</body>
</html>
