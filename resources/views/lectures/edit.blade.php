<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать лекцию — {{ $lecture->title }}</title>

    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>


    <style>
        .trix-color-toolbar {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 10px;
            background: var(--gray-50);
            border: 1px solid var(--color-border);
            border-bottom: none;
            border-radius: var(--r-md) var(--r-md) 0 0;
            flex-wrap: wrap;
        }

        .color-swatch {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #ccc;
            cursor: pointer;
        }

        .color-swatch.active {
            outline: 2px solid var(--gray-900);
        }

        #reset-color {
            padding: 2px 8px;
            border-radius: var(--r-sm);
            border: 1px solid var(--color-border);
            background: white;
            cursor: pointer;
        }
    </style>
</head>

<body>

@include('components.menu')

<div class="layout">

    {{-- SIDEBAR --}}
    <aside class="sidebar">

        <p class="sidebar-section-title">Навигация</p>

        <a href="{{ route('home') }}" class="sidebar-link">
            Все курсы
        </a>

        <a href="{{ route('courses.show', $lecture->course) }}" class="sidebar-link">
            ← К курсу
        </a>

        <p class="sidebar-section-title" style="margin-top: 2rem;">Курс</p>

        <div style="padding: 0 0.75rem;">
            <p style="font-size: 13px; font-weight: 600; color: var(--gray-800);">
                {{ $lecture->course->title }}
            </p>
        </div>

    </aside>

    {{-- MAIN --}}
    <main class="main">

        {{-- BREADCRUMB --}}
        <nav style="display:flex; gap:8px; font-size:13px; color:var(--color-text-muted); margin-bottom:1.5rem;">

            <a href="{{ route('home') }}">Курсы</a>

            <span>›</span>

            <a href="{{ route('courses.show', $lecture->course) }}">
                {{ $lecture->course->title }}
            </a>

            <span>›</span>

            <span style="color:var(--gray-600); font-weight:500;">
                Редактировать лекцию
            </span>

        </nav>

        {{-- HEADER --}}
        <div class="page-header">
            <h1 class="page-header__title">
                Редактировать лекцию
            </h1>
        </div>

        <div style="max-width: 760px;">

            <div class="panel" style="padding: 2rem;">

                {{-- ERRORS --}}
                @if($errors->any())
                    <div style="
                        background:#fff1f2;
                        border:1px solid #fecdd3;
                        padding:12px 14px;
                        border-radius:var(--r-md);
                        margin-bottom:1.5rem;
                    ">
                        <ul style="margin:0; padding-left:16px; font-size:13px; color:#e11d48;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.lectures.update', $lecture) }}"
                      method="POST"
                      enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    {{-- TITLE --}}
                    <div style="margin-bottom:1.2rem;">
                        <label style="font-size:13px;font-weight:600;">
                            Название лекции
                        </label>

                        <input type="text"
                               name="title"
                               value="{{ old('title', $lecture->title) }}"
                               style="
                                    width:100%;
                                    padding:10px 14px;
                                    border:1px solid var(--color-border);
                                    border-radius:var(--r-md);
                               ">
                    </div>

                    {{-- CONTENT TYPE --}}
                    <div style="margin-bottom:1.2rem;">
                        <label style="font-size:13px;font-weight:600;">
                            Способ контента
                        </label>

                        <div style="display:flex; gap:16px; margin-top:6px;">
                            <label><input type="radio" name="content_source_radio" value="manual" checked> Текст</label>
                            <label><input type="radio" name="content_source_radio" value="pdf"> PDF</label>
                            <label><input type="radio" name="content_source_radio" value="word"> Word</label>
                        </div>
                    </div>

                    {{-- MANUAL --}}
                    <div id="manual-content-block">

                        <div class="trix-color-toolbar">
                            @foreach(['e74c3c','3498db','2ecc71','f1c40f','000000'] as $c)
                                <button type="button"
                                        class="color-swatch"
                                        data-color="#{{ $c }}"
                                        style="background:#{{ $c }}"></button>
                            @endforeach
                            <button type="button" id="reset-color">×</button>
                        </div>

                        <input id="content-input" type="hidden" name="content"
                               value="{{ old('content', $lecture->content) }}">

                        <trix-editor input="content-input"
                                     style="min-height:300px;"></trix-editor>

                    </div>

                    {{-- PDF --}}
                    <div id="pdf-block" style="display:none; margin-top:1.5rem;">
                        <input type="file" name="pdf">
                    </div>

                    {{-- WORD --}}
                    <div id="word-block" style="display:none; margin-top:1.5rem;">
                        <input type="file" name="word">
                    </div>

                    {{-- ACTIONS --}}
                    <div style="margin-top:2rem; display:flex; gap:10px;">

                        <button class="btn btn-primary">
                            Сохранить изменения
                        </button>

                        <a href="{{ route('courses.show', $lecture->course) }}"
                           class="btn btn-ghost">
                            Отмена
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const manual = document.getElementById('manual-content-block');
    const pdf = document.getElementById('pdf-block');
    const word = document.getElementById('word-block');

    document.querySelectorAll('input[name="content_source_radio"]').forEach(r => {
        r.addEventListener('change', () => {

            const v = document.querySelector('input[name="content_source_radio"]:checked').value;

            manual.style.display = v === 'manual' ? 'block' : 'none';
            pdf.style.display = v === 'pdf' ? 'block' : 'none';
            word.style.display = v === 'word' ? 'block' : 'none';
        });
    });

});
</script>

</body>
</html>
