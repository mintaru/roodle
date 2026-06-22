<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $lecture->title }} — Roodle</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/trix.min.css') }}">
    <script src="{{ asset('js/trix.min.js') }}"></script>
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/lecture-content.css') }}">
    <script>
        if (localStorage.getItem('dark-mode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body>

@include('components.menu')

<div class="lecture-layout">

    {{-- BREADCRUMB --}}
    <nav style="
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 1.5rem;
        font-size: 13px;
        color: var(--color-text-muted);
    ">
        <a href="{{ route('home') }}" style="color:var(--color-text-muted);text-decoration:none;transition:color 0.2s;"
           onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">
            Курсы
        </a>
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
        <a href="{{ route('courses.show', $course) }}" style="color:var(--color-text-muted);text-decoration:none;transition:color 0.2s;"
           onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">
            {{ $course->title }}
        </a>
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
        <span style="color:var(--gray-600);font-weight:500;">{{ $lecture->title }}</span>
    </nav>

    {{-- ЗАГОЛОВОК --}}
    <div class="page-header">
        <h1 class="page-header__title">{{ $lecture->title }}</h1>
    </div>

    {{-- ПРОГРЕСС-БАР --}}
    @if ($lecture->pdf_path && strtolower(pathinfo($lecture->pdf_path, PATHINFO_EXTENSION)) === 'pdf')
    <div class="lecture-progress">
        <div class="lecture-progress__header">
            <span class="lecture-progress__label">Прогресс чтения</span>
            <span class="lecture-progress__value" id="progress-text">Страница 1 из —</span>
        </div>
        <div class="lecture-progress__bar">
            <div class="lecture-progress__fill" id="progress-fill"></div>
        </div>
    </div>
    @endif

    {{-- PDF VIEWER --}}
    @if ($lecture->pdf_path)
        @php
            $ext = strtolower(pathinfo($lecture->pdf_path, PATHINFO_EXTENSION));
            $fileUrl = route('lectures.file', [$course, $lecture]);
        @endphp

        @if ($ext === 'pdf')
        <div class="pdf-viewer-wrap">

            <div class="pdf-toolbar">
                <div class="pdf-toolbar__left">
                    <button id="pdf-prev" class="pdf-btn" title="Предыдущая страница">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                    </button>
                    <span class="pdf-page-info">
                        Страница <span id="pdf-page-num">1</span> из <span id="pdf-page-count">—</span>
                    </span>
                    <button id="pdf-next" class="pdf-btn" title="Следующая страница">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                    </button>
                </div>
                <div class="pdf-toolbar__right">
                    <button id="pdf-zoom-out" class="pdf-btn">−</button>
                    <span id="pdf-zoom-label" class="pdf-page-info">100%</span>
                    <button id="pdf-zoom-in" class="pdf-btn">+</button>
                    <a href="{{ $fileUrl }}" target="_blank" class="pdf-btn pdf-btn--open" title="Открыть в приложении">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><path d="M15 3h6v6"/><path d="M10 14L21 3"/></svg>
                    </a>
                    <a href="{{ $fileUrl }}" download class="pdf-btn" title="Скачать PDF">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    </a>
                </div>
            </div>

            <div class="pdf-canvas-wrap" id="pdf-canvas-wrap">
                <canvas id="pdf-canvas"></canvas>
            </div>

        </div>

        <script>window.__pdfUrl = '{{ $fileUrl }}';</script>
        <script type="module">
            import * as pdfjsLib from '{{ asset('js/pdfjs/build/pdf.mjs') }}';
            pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ asset('js/pdfjs/build/pdf.worker.mjs') }}';

            const url    = window.__pdfUrl;
            const canvas = document.getElementById('pdf-canvas');
            const ctx    = canvas.getContext('2d');
            const wrap   = document.getElementById('pdf-canvas-wrap');
            const progressFill = document.getElementById('progress-fill');
            const progressText = document.getElementById('progress-text');

            let pdfDoc     = null;
            let pageNum    = 1;
            let baseScale  = 1.0;
            let zoomLevel  = 1.0;
            let scale      = 1.0;
            let rendering  = false;
            let pendingPage = null;

            function updateProgress() {
                if (!pdfDoc) return;
                const total   = pdfDoc.numPages;
                const pct     = Math.round((pageNum / total) * 100);
                const fill    = document.getElementById('progress-fill');
                const text    = document.getElementById('progress-text');
                if (fill) {
                    fill.style.width = pct + '%';
                    fill.classList.toggle('done', pageNum === total);
                }
                if (text) {
                    text.textContent = 'Страница ' + pageNum + ' из ' + total;
                }
            }

            function renderPage(num) {
                if (rendering) { pendingPage = num; return; }
                rendering = true;

                pdfDoc.getPage(num).then(page => {
                    const dpr      = window.devicePixelRatio || 1;
                    const viewport = page.getViewport({ scale });

                    canvas.width  = viewport.width  * dpr;
                    canvas.height = viewport.height * dpr;
                    canvas.style.width  = viewport.width  + 'px';
                    canvas.style.height = viewport.height + 'px';

                    page.render({
                        canvasContext: ctx,
                        viewport,
                        transform: [dpr, 0, 0, dpr, 0, 0]
                    }).promise.then(() => {
                        rendering = false;
                        document.getElementById('pdf-page-num').textContent = num;
                        updateProgress();
                        if (pendingPage !== null) {
                            const next = pendingPage;
                            pendingPage = null;
                            renderPage(next);
                        }
                    });
                });
            }

            function fitToWidth() {
                if (!pdfDoc) return;
                pdfDoc.getPage(pageNum).then(page => {
                    const vp = page.getViewport({ scale: 1 });
                    const availableWidth = wrap.getBoundingClientRect().width;
                    baseScale = availableWidth >= vp.width
                        ? 1.0
                        : availableWidth / vp.width;
                    scale = baseScale * zoomLevel;
                    document.getElementById('pdf-zoom-label').textContent =
                        Math.round(zoomLevel * 100) + '%';
                    renderPage(pageNum);
                });
            }

            pdfjsLib.getDocument({ url, withCredentials: true }).promise.then(pdf => {
                pdfDoc = pdf;
                document.getElementById('pdf-page-count').textContent = pdf.numPages;
                fitToWidth();
            });

            document.getElementById('pdf-prev').addEventListener('click', () => {
                if (pageNum <= 1) return;
                pageNum--;
                renderPage(pageNum);
            });

            document.getElementById('pdf-next').addEventListener('click', () => {
                if (pageNum >= pdfDoc.numPages) return;
                pageNum++;
                renderPage(pageNum);
            });

            document.getElementById('pdf-zoom-in').addEventListener('click', () => {
                zoomLevel = Math.min(zoomLevel + 0.25, 4.0);
                scale = baseScale * zoomLevel;
                document.getElementById('pdf-zoom-label').textContent =
                    Math.round(zoomLevel * 100) + '%';
                renderPage(pageNum);
            });

            document.getElementById('pdf-zoom-out').addEventListener('click', () => {
                zoomLevel = Math.max(zoomLevel - 0.25, 0.25);
                scale = baseScale * zoomLevel;
                document.getElementById('pdf-zoom-label').textContent =
                    Math.round(zoomLevel * 100) + '%';
                renderPage(pageNum);
            });

            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(fitToWidth, 150);
            });
        </script>
        @endif
    @elseif ($lecture->content)
        <div class="lecture-content">
            @if ($lecture->content_type === 'html')
                {!! $lecture->content !!}
            @else
                <div class="lecture-content__text">{{ $lecture->content }}</div>
            @endif
        </div>
    @endif

</div>
</body>
</html>
