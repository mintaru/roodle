<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ $lecture->title }} — Roodle</title>

    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">



    <link rel="stylesheet" href="{{ asset('css/trix.min.css') }}">
    <script src="{{ asset('js/trix.min.js') }}"></script>

    <script defer src="{{ asset('js/alpine.min.js') }}"></script>

    <style>
        .lecture-content {
            word-break: break-word;
            overflow-wrap: break-word;
            color: var(--color-text-secondary);
            line-height: 1.8;
            font-size: 15px;
        }

        .lecture-content img {
            max-width: 100%;
            height: auto;
            border-radius: var(--r-lg);
            margin: 1.5rem 0;
            box-shadow: var(--shadow-md);
        }

        .lecture-content h1,
        .lecture-content h2,
        .lecture-content h3,
        .lecture-content h4,
        .lecture-content h5,
        .lecture-content h6 {
            color: var(--color-text-primary);
            font-weight: 700;
            margin: 1.5rem 0 1rem;
            line-height: 1.3;
        }

        .lecture-content p {
            margin-bottom: 1rem;
        }

        .lecture-content a {
            color: var(--teal-600);
            text-decoration: underline;
        }

        .lecture-content ul,
        .lecture-content ol {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .lecture-content blockquote {
            border-left: 4px solid var(--teal-500);
            padding-left: 1rem;
            color: var(--color-text-secondary);
            margin: 1.5rem 0;
            font-style: italic;
        }

        .lecture-content pre {
            background: var(--gray-900);
            color: white;
            padding: 1rem;
            border-radius: var(--r-lg);
            overflow-x: auto;
            margin: 1.5rem 0;
        }

        .lecture-content code {
            background: var(--gray-100);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 13px;
            color: var(--red-500);
        }

        .lecture-content-text {
            white-space: pre-wrap;
        }
    </style>
</head>

<body>

@include('components.menu')

<div class="layout">

    {{-- SIDEBAR --}}
    <aside class="sidebar">

        <p class="sidebar-section-title">
            Навигация
        </p>

        <a href="{{ route('courses.show', $lecture->course) }}" class="sidebar-link">

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

        <a href="{{ route('home') }}" class="sidebar-link">

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

        <p class="sidebar-section-title" style="margin-top: 2rem;">
            Курс
        </p>

        <div style="padding: 0 0.75rem;">

            <p style="
                font-size: 13px;
                font-weight: 600;
                color: var(--gray-800);
                line-height: 1.4;
            ">
                {{ $lecture->course->title }}
            </p>

        </div>

    </aside>

    {{-- MAIN --}}
    <main class="main">

        {{-- BREADCRUMB --}}
        <nav style="
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.75rem;
            font-size: 13px;
            color: var(--color-text-muted);
        ">

            <a href="{{ route('home') }}"
               style="color: var(--color-text-muted); text-decoration: none;">

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

            <a href="{{ route('courses.show', $lecture->course) }}"
               style="color: var(--color-text-muted); text-decoration: none;">

                {{ $lecture->course->title }}

            </a>

            <svg width="14"
                 height="14"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">

                <path d="M9 18l6-6-6-6"/>

            </svg>

            <span style="color: var(--gray-600); font-weight: 500;">

                {{ $lecture->title }}

            </span>

        </nav>

        {{-- PAGE HEADER --}}
        <div class="page-header">

            <h1 class="page-header__title">
                {{ $lecture->title }}
            </h1>

        </div>

        <div class="grid-3-1">

            {{-- MAIN CONTENT --}}
            <div style="display:flex; flex-direction:column; gap:1rem;">

                {{-- FILE PANEL --}}
                @if ($lecture->pdf_path)

                    @php
                        $ext = strtolower(pathinfo($lecture->pdf_path, PATHINFO_EXTENSION));
                        $fileUrl = route('lectures.file', [$lecture->course, $lecture]);
                    @endphp

                    <div class="panel" style="padding: 1.5rem;">

                        <div style="
                            display:flex;
                            align-items:center;
                            justify-content:space-between;
                            margin-bottom:1rem;
                        ">

                            <h2 class="panel__title">
                                {{ $ext === 'pdf' ? '📄 PDF материал' : '📝 Word документ' }}
                            </h2>

                            <a href="{{ $fileUrl }}"
                               download
                               class="btn btn-secondary">

                                Скачать

                            </a>

                        </div>

                        @if ($ext === 'pdf')

                            <iframe
                                src="{{ $fileUrl }}"
                                width="100%"
                                height="800"
                                style="
                                    border:none;
                                    border-radius:var(--r-lg);
                                    overflow:hidden;
                                "
                            ></iframe>

                        @else

                            <div style="
                                padding: 12px 14px;
                                background: var(--sky-50);
                                border: 1px solid var(--sky-100);
                                border-radius: var(--r-md);
                                font-size: 13px;
                                color: var(--sky-700);
                            ">

                                💡 Word документ отображается ниже как форматированный текст.

                            </div>

                        @endif

                    </div>

                @endif

                {{-- LECTURE CONTENT --}}
                <div class="panel" style="padding: 2rem;">

                    <div class="panel__header">

                        <h2 class="panel__title">
                            Содержание лекции
                        </h2>

                    </div>

                    <div class="lecture-content {{ ($lecture->content_type ?? 'text') === 'html' ? 'trix-content' : 'lecture-content-text' }}">

                        @if (($lecture->content_type ?? 'text') === 'html')

                            {!! $lecture->content !!}

                        @else

                            {!! nl2br(e($lecture->content ?? '')) !!}

                        @endif

                    </div>

                </div>

            </div>

            {{-- SIDEBAR INFO --}}
            <div style="
                display:flex;
                flex-direction:column;
                gap:1rem;
            ">

                {{-- INFO CARD --}}
                <div class="panel" style="padding:1.5rem;">

                    <div class="panel__title" style="margin-bottom:1.25rem;">
                        📚 Информация
                    </div>

                    <div style="
                        display:flex;
                        flex-direction:column;
                        gap:1.25rem;
                    ">

                        <div>

                            <div style="
                                font-size:12px;
                                color:var(--color-text-muted);
                                text-transform:uppercase;
                                font-weight:600;
                                margin-bottom:6px;
                            ">
                                Курс
                            </div>

                            <a href="{{ route('courses.show', $lecture->course) }}"
                               style="
                                    color: var(--teal-600);
                                    text-decoration: none;
                                    font-weight: 600;
                               ">

                                {{ $lecture->course->title }}

                            </a>

                        </div>

                        @if ($lecture->duration)

                            <div>

                                <div style="
                                    font-size:12px;
                                    color:var(--color-text-muted);
                                    text-transform:uppercase;
                                    font-weight:600;
                                    margin-bottom:6px;
                                ">
                                    Длительность
                                </div>

                                <div style="font-weight:600;">

                                    ⏱️ {{ $lecture->duration }} минут

                                </div>

                            </div>

                        @endif

                        <div>

                            <div style="
                                font-size:12px;
                                color:var(--color-text-muted);
                                text-transform:uppercase;
                                font-weight:600;
                                margin-bottom:6px;
                            ">
                                Добавлено
                            </div>

                            <div style="font-weight:500;">

                                {{ $lecture->created_at->format('d.m.Y') }}

                            </div>

                        </div>

                    </div>

                </div>

                {{-- TYPE BADGE --}}
                <div class="panel" style="
                    padding:1.25rem;
                    text-align:center;
                ">

                    @if (($lecture->content_type ?? 'text') === 'html')

                        <span class="badge badge-teal">
                            📝 Форматированный текст
                        </span>

                    @else

                        <span class="badge badge-sky">
                            📄 Обычный текст
                        </span>

                    @endif

                </div>

            </div>

        </div>

    </main>

</div>

</body>
</html>
