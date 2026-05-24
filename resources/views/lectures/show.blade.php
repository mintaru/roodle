<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Лекция - Roodle</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
            .lecture-content {
        word-break: break-word;
        overflow-wrap: break-word;
    }

    .lecture-content img {
        max-width: 100%;
        height: auto;
        border-radius: var(--r-lg);
        margin: 1.5rem 0;
        box-shadow: var(--shadow-md);
    }

    .lecture-content p {
        margin-bottom: 1rem;
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

    .lecture-content a {
        color: var(--teal-600);
        text-decoration: underline;
        transition: var(--transition);
    }

    .lecture-content a:hover {
        color: var(--teal-800);
    }

    .lecture-content ul,
    .lecture-content ol {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }

    .lecture-content li {
        margin-bottom: 0.5rem;
    }

    .lecture-content blockquote {
        border-left: 4px solid var(--teal-500);
        padding-left: 1rem;
        color: var(--color-text-secondary);
        margin: 1.5rem 0;
        font-style: italic;
    }

    .lecture-content code {
        background: var(--gray-100);
        padding: 2px 6px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
        color: var(--red-500);
    }

    .lecture-content pre {
        background: var(--gray-900);
        color: #fff;
        padding: 1rem;
        border-radius: var(--r-lg);
        overflow-x: auto;
        margin: 1.5rem 0;
    }

    .trix-content {
        font-size: 15px;
    }

    .lecture-content-text {
        white-space: pre-wrap;
    }
    </style>
</head>

<body>



    @include('components.menu')

    <div class="main">
        <!-- BREADCRUMB & BACK BUTTON -->
        <div style="margin-bottom: 2rem;">
            <x-back-button :url="route('courses.show', $lecture->course)" text="К курсу" />
        </div>

        <!-- LECTURE HERO -->
        <div class="course-hero" style="margin-bottom: 2rem;">
            <div class="course-hero__breadcrumb">
                <a href="{{ route('admin.courses.index') }}"
                    style="color: rgba(255,255,255,.7); text-decoration: none;">Курсы</a>
                <span style="opacity: .5; margin: 0 8px;">/</span>
                <a href="{{ route('courses.show', $lecture->course) }}"
                    style="color: rgba(255,255,255,.7); text-decoration: none;">{{ $lecture->course->title }}</a>
            </div>
            <h1 class="course-hero__title">{{ $lecture->title }}</h1>

            @if ($lecture->pdf_path)
            {{-- Определяем тип файла --}}
            @php
                $ext = strtolower(pathinfo($lecture->pdf_path, PATHINFO_EXTENSION));
                $fileUrl = route('lectures.file', [$lecture->course, $lecture]);
            @endphp

            <div class="panel" style="margin-bottom: 2rem;">
                <div class="panel__header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2 class="panel__title">
                        {{ $ext === 'pdf' ? '📄 PDF материал' : '📝 Word документ' }}
                    </h2>
                    <a href="{{ $fileUrl }}" download class="btn btn-white" style="font-size: 13px;">
                        ⬇️ Скачать
                    </a>
                </div>

                @if ($ext === 'pdf')
                    {{-- PDF: нативный браузерный рендер --}}
                    <iframe
                        src="{{ $fileUrl }}"
                        width="100%"
                        height="800px"
                        style="border: none; border-radius: var(--r-lg);"
                        loading="lazy"
                    ></iframe>
                @else
                    {{-- Word: показываем сконвертированный HTML (уже есть в content) --}}
                    {{-- Файл доступен для скачивания кнопкой выше --}}
                    <div class="panel" style="background: var(--gray-50); padding: 1rem; border-radius: var(--r-md);">
                        <span style="color: var(--color-text-muted); font-size: 14px;">
                            💡 Word документ отображается как форматированный текст ниже.
                            Для просмотра оригинала — скачайте файл.
                        </span>
                    </div>
                @endif
            </div>
        @endif
        </div>

        <!-- LECTURE INFO PANEL -->
        <div class="grid-3-1" style="margin-bottom: 2rem;">
            <!-- CONTENT -->
            <div class="panel">
                <div class="panel__header">
                    <h2 class="panel__title">Содержание лекции</h2>
                </div>

                <div class="lecture-content {{ ($lecture->content_type ?? 'text') === 'html' ? 'trix-content' : 'lecture-content-text' }}"
                    style="color: var(--color-text-secondary); line-height: 1.8; font-size: 15px;">
                    @if (($lecture->content_type ?? 'text') === 'html')
                        {!! $lecture->content !!}
                    @else
                        {!! nl2br(e($lecture->content ?? '')) !!}
                    @endif
                </div>
            </div>

            <!-- SIDEBAR INFO -->
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <!-- COURSE INFO -->
                <div class="panel">
                    <div class="panel__title" style="margin-bottom: 1rem;">📚 Информация</div>

                    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                        <div>
                            <div
                                style="font-size: 12px; color: var(--color-text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: .5px; margin-bottom: 6px;">
                                Курс</div>
                            <a href="{{ route('courses.show', $lecture->course) }}"
                                style="color: var(--teal-600); text-decoration: none; font-weight: 600; font-size: 15px; transition: var(--transition);"
                                onmouseover="this.style.color='var(--teal-800)'"
                                onmouseout="this.style.color='var(--teal-600)'">
                                {{ $lecture->course->title }}
                            </a>
                        </div>

                        @if ($lecture->duration)
                            <div>
                                <div
                                    style="font-size: 12px; color: var(--color-text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: .5px; margin-bottom: 6px;">
                                    ⏱️ Длительность</div>
                                <div style="font-weight: 600; color: var(--color-text-primary);">
                                    {{ $lecture->duration }} минут</div>
                            </div>
                        @endif

                        <div>
                            <div
                                style="font-size: 12px; color: var(--color-text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: .5px; margin-bottom: 6px;">
                                📅 Добавлено</div>
                            <div style="font-weight: 500; color: var(--color-text-secondary);">
                                {{ $lecture->created_at->format('d.m.Y') }}</div>
                        </div>
                    </div>
                </div>

                <!-- MATERIAL TYPE BADGE -->
                <div class="panel" style="text-align: center; padding: 1.25rem;">
                    @if (($lecture->content_type ?? 'text') === 'html')
                        <span class="badge badge-teal">📝 Форматированный текст</span>
                    @else
                        <span class="badge badge-sky">📄 Обычный текст</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>

</html>
