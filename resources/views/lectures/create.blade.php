<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Добавить лекцию — {{ $course->title }}</title>

    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">



    <link rel="stylesheet" href="{{ asset('css/trix.min.css') }}">
    <script src="{{ asset('js/trix.min.js') }}"></script>

    <script defer src="{{ asset('js/alpine.min.js') }}"></script>

    <style>
        .lecture-trix {
            border: 1px solid var(--color-border);
            border-radius: 0 0 var(--r-lg) var(--r-lg);
            min-height: 320px;
            padding: 1rem;
            font-size: 15px;
            background: var(--color-surface);
        }

        .lecture-trix:focus {
            border-color: var(--teal-400);
            box-shadow: 0 0 0 3px rgba(0,181,165,.1);
        }

        .trix-toolbar {
            border: 1px solid var(--color-border);
            border-bottom: none;
            border-radius: var(--r-lg) var(--r-lg) 0 0;
            background: var(--gray-50);
            padding: 10px;
        }

        .trix-button-group {
            border-color: var(--color-border) !important;
        }

        .trix-color-toolbar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 14px;
            background: var(--gray-50);
            border: 1px solid var(--color-border);
            border-bottom: none;
            border-radius: var(--r-lg) var(--r-lg) 0 0;
            flex-wrap: wrap;
        }

        .color-swatch {
            width: 24px;
            height: 24px;
            border-radius: 999px;
            border: 2px solid white;
            box-shadow: 0 0 0 1px rgba(0,0,0,.15);
            cursor: pointer;
            transition: .2s;
        }

        .color-swatch:hover {
            transform: scale(1.12);
        }

        .color-swatch.active {
            transform: scale(1.12);
            box-shadow: 0 0 0 2px var(--gray-800);
        }

        #reset-color {
            border: 1px solid var(--color-border);
            background: white;
            border-radius: var(--r-md);
            padding: 6px 10px;
            font-size: 13px;
            cursor: pointer;
        }

        #custom-color {
            width: 32px;
            height: 32px;
            border: none;
            background: transparent;
            cursor: pointer;
        }

        .source-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .source-option input {
            display: none;
        }

        .source-option span {
            display: inline-flex;
            align-items: center;
            padding: 10px 16px;
            border-radius: var(--r-full);
            border: 1px solid var(--color-border);
            background: white;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700);
            cursor: pointer;
            transition: .2s;
        }

        .source-option input:checked + span {
            background: var(--teal-500);
            border-color: var(--teal-500);
            color: white;
            box-shadow: var(--shadow-accent);
        }

        .upload-card {
            border: 2px dashed var(--color-border-2);
            border-radius: var(--r-lg);
            padding: 1.5rem;
            background: var(--gray-50);
        }
    </style>
</head>
<body>

@include('components.menu')

<div class="layout">

    {{-- Sidebar --}}
    <aside class="sidebar">

        <p class="sidebar-section-title">
            Навигация
        </p>

        <a href="{{ route('courses.show', $course) }}" class="sidebar-link">

            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>

            К курсу

        </a>

        <a href="{{ route('home') }}" class="sidebar-link">

            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            </svg>

            Все курсы

        </a>

        <p class="sidebar-section-title" style="margin-top: 2rem;">
            Курс
        </p>

        <div style="padding: 0 .75rem;">

            <p style="
                font-size: 13px;
                font-weight: 600;
                color: var(--gray-800);
                line-height: 1.4;
            ">
                {{ $course->title }}
            </p>

        </div>

    </aside>

    {{-- Main --}}
    <main class="main">

        {{-- Breadcrumb --}}
        <nav style="
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.75rem;
            font-size: 13px;
            color: var(--color-text-muted);
        ">

            <a href="{{ route('home') }}"
               style="color: var(--color-text-muted); text-decoration: none;"
               onmouseover="this.style.color='var(--teal-600)'"
               onmouseout="this.style.color='var(--color-text-muted)'">

                Курсы

            </a>

            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 18l6-6-6-6"/>
            </svg>

            <a href="{{ route('courses.show', $course) }}"
               style="color: var(--color-text-muted); text-decoration: none;"
               onmouseover="this.style.color='var(--teal-600)'"
               onmouseout="this.style.color='var(--color-text-muted)'">

                {{ $course->title }}

            </a>

            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 18l6-6-6-6"/>
            </svg>

            <span style="color: var(--gray-600); font-weight: 500;">
                Добавить лекцию
            </span>

        </nav>

        {{-- Header --}}
        <div class="page-header">

            <h1 class="page-header__title">
                Добавить лекцию
            </h1>

        </div>

        <div style="max-width: 920px;">

            {{-- Card --}}
            <div class="panel" style="padding: 2rem;">

                {{-- Course badge --}}
                <div style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 10px 14px;
                    background: var(--teal-50);
                    border-radius: var(--r-md);
                    margin-bottom: 2rem;
                    border: 1px solid var(--teal-100);
                ">

                    <svg width="16"
                         height="16"
                         fill="none"
                         stroke="var(--teal-600)"
                         stroke-width="2"
                         viewBox="0 0 24 24">

                        <path d="M4 19.5A2.5 2.5 0 016.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>

                    </svg>

                    <span style="
                        font-size: 13px;
                        font-weight: 600;
                        color: var(--teal-700);
                    ">
                        {{ $course->title }}
                    </span>

                </div>

                <form
                    action="{{ route('lectures.store', $course) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    id="lecture-form"
                >

                    @csrf

                    <input type="hidden"
                           name="content_source"
                           id="content-source-hidden"
                           value="manual">

                    {{-- Title --}}
                    <div style="margin-bottom: 1.5rem;">

                        <label for="title"
                               style="
                                    display: block;
                                    font-size: 13px;
                                    font-weight: 600;
                                    color: var(--gray-700);
                                    margin-bottom: 8px;
                               ">

                            Название лекции
                            <span style="color: var(--red-400);">*</span>

                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            required
                            placeholder="Например: Введение в алгоритмы"
                            style="
                                width: 100%;
                                padding: 10px 14px;
                                border: 1px solid var(--color-border);
                                border-radius: var(--r-md);
                                font-size: 14px;
                                font-family: var(--font-body);
                                background: var(--color-surface);
                                outline: none;
                            "
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >

                    </div>

                    {{-- Content source --}}
                    <div style="margin-bottom: 2rem;">

                        <label style="
                            display: block;
                            font-size: 13px;
                            font-weight: 600;
                            color: var(--gray-700);
                            margin-bottom: 12px;
                        ">
                            Способ добавления контента
                        </label>

                        <div class="source-tabs">

                            <label class="source-option">

                                <input type="radio"
                                       name="content_source_radio"
                                       value="manual"
                                       checked>

                                <span>
                                    Ввести текст вручную
                                </span>

                            </label>

                            <label class="source-option">

                                <input type="radio"
                                       name="content_source_radio"
                                       value="pdf">

                                <span>
                                    Загрузить PDF
                                </span>

                            </label>

                            <label class="source-option">

                                <input type="radio"
                                       name="content_source_radio"
                                       value="word">

                                <span>
                                    Загрузить Word
                                </span>

                            </label>

                        </div>

                    </div>

                    {{-- Manual editor --}}
                    <div id="manual-content-block">

                        <label style="
                            display: block;
                            font-size: 13px;
                            font-weight: 600;
                            color: var(--gray-700);
                            margin-bottom: 8px;
                        ">
                            Текст лекции
                        </label>

                        {{-- Color toolbar --}}
                        <div class="trix-color-toolbar" id="trix-color-toolbar">

                            <span style="
                                font-size: 13px;
                                font-weight: 600;
                                color: var(--gray-700);
                            ">
                                Цвет текста:
                            </span>

                            @foreach(['e74c3c','e67e22','f1c40f','2ecc71','3498db','9b59b6','000000','ffffff'] as $color)

                                <button
                                    type="button"
                                    class="color-swatch"
                                    data-color="#{{ $color }}"
                                    style="background:#{{ $color }}"
                                    title="#{{ $color }}"
                                ></button>

                            @endforeach

                            <button type="button"
                                    id="reset-color">

                                ✕

                            </button>

                            <input type="color"
                                   id="custom-color">

                        </div>

                        <input id="content-input"
                               type="hidden"
                               name="content">

                        <trix-editor
                            input="content-input"
                            class="lecture-trix"
                        ></trix-editor>

                    </div>

                    {{-- PDF --}}
                    <div id="pdf-block"
                         class="upload-card"
                         style="display:none; margin-top: 1.5rem;">

                        <label style="
                            display:block;
                            font-size:13px;
                            font-weight:600;
                            color:var(--gray-700);
                            margin-bottom:10px;
                        ">
                            PDF файл
                        </label>

                        <input type="file"
                               name="pdf"
                               id="pdf-input"
                               accept="application/pdf">

                        <div style="
                            display:grid;
                            grid-template-columns:1fr 1fr;
                            gap:1rem;
                            margin-top:1rem;
                        ">

                            <div>

                                <label style="
                                    display:block;
                                    font-size:12px;
                                    margin-bottom:6px;
                                    color:var(--gray-600);
                                ">
                                    С какой страницы
                                </label>

                                <input type="number"
                                       name="from_page"
                                       min="1"
                                       style="
                                            width:100%;
                                            padding:10px 14px;
                                            border:1px solid var(--color-border);
                                            border-radius:var(--r-md);
                                       ">

                            </div>

                            <div>

                                <label style="
                                    display:block;
                                    font-size:12px;
                                    margin-bottom:6px;
                                    color:var(--gray-600);
                                ">
                                    По какую страницу
                                </label>

                                <input type="number"
                                       name="to_page"
                                       min="1"
                                       style="
                                            width:100%;
                                            padding:10px 14px;
                                            border:1px solid var(--color-border);
                                            border-radius:var(--r-md);
                                       ">

                            </div>

                        </div>

                    </div>

                    {{-- Word --}}
                    <div id="word-block"
                         class="upload-card"
                         style="display:none; margin-top: 1.5rem;">

                        <label style="
                            display:block;
                            font-size:13px;
                            font-weight:600;
                            color:var(--gray-700);
                            margin-bottom:10px;
                        ">
                            Word файл
                        </label>

                        <input type="file"
                               name="word"
                               id="word-input"
                               accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">

                        <p style="
                            margin-top:12px;
                            font-size:12px;
                            color:var(--color-text-muted);
                        ">
                            Поддерживаются .doc и .docx. Форматирование сохранится автоматически.
                        </p>

                    </div>

                    {{-- Actions --}}
                    <div style="
                        display:flex;
                        align-items:center;
                        gap:.75rem;
                        margin-top:2rem;
                        flex-wrap:wrap;
                    ">

                        <button type="submit"
                                class="btn btn-primary"
                                style="padding:10px 24px;">

                            <svg width="16"
                                 height="16"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 viewBox="0 0 24 24">

                                <path d="M12 5v14"/>
                                <path d="M5 12h14"/>

                            </svg>

                            Создать лекцию

                        </button>

                        <a href="{{ route('courses.show', $course) }}"
                           class="btn btn-ghost">

                            Отмена

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </main>

</div>

<script>
    const COLORS = [
        '#e74c3c',
        '#e67e22',
        '#f1c40f',
        '#2ecc71',
        '#3498db',
        '#9b59b6',
        '#000000',
        '#ffffff'
    ];

    COLORS.forEach(color => {
        const key = 'color' + color.replace('#', '');
        Trix.config.textAttributes[key] = {
            style: { color },
            inheritable: true,
            parser: el => el.style.color === color
        };
    });

    function initTrixColorToolbar() {
        const editor = document.querySelector('trix-editor.lecture-trix');
        if (!editor) return;

        function clearColors() {
            COLORS.forEach(color => {
                editor.editor.deactivateAttribute('color' + color.replace('#', ''));
            });
            document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
        }

        function applyColor(color) {
            clearColors();
            editor.editor.activateAttribute('color' + color.replace('#', ''));
            const btn = document.querySelector(`.color-swatch[data-color="${color}"]`);
            if (btn) btn.classList.add('active');
        }

        document.querySelectorAll('.color-swatch').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                applyColor(btn.dataset.color);
            });
        });

        document.getElementById('reset-color').addEventListener('click', (e) => {
            e.preventDefault();
            clearColors();
        });

        const customPicker = document.getElementById('custom-color');
        customPicker.addEventListener('input', (e) => {
            const hex = e.target.value;
            const key = 'color' + hex.replace('#', '');
            if (!Trix.config.textAttributes[key]) {
                Trix.config.textAttributes[key] = {
                    style: { color: hex },
                    inheritable: true,
                    parser: el => el.style.color === hex
                };
            }
            clearColors();
            editor.editor.activateAttribute(key);
        });

        document.addEventListener('trix-file-accept', function(e) {
            if (!e.file.type.startsWith('image/')) return;
            e.preventDefault();
            const formData = new FormData();
            formData.append('attachment', e.file);
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            fetch('{{ route("lectures.upload-attachment") }}', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(result => {
                if (result.url) {
                    editor.editor.insertHTML(
                        '<figure class="attachment attachment--preview attachment--image"><img src="' + result.url + '" alt="' + e.file.name + '" /></figure>'
                    );
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                alert('Ошибка при загрузке изображения');
            });
        });
    }

    initTrixColorToolbar();
    document.addEventListener('trix-initialize', initTrixColorToolbar);

document.addEventListener('DOMContentLoaded', function() {

    const manualBlock = document.getElementById('manual-content-block');

    const pdfBlock = document.getElementById('pdf-block');

    const wordBlock = document.getElementById('word-block');

    const pdfInput = document.getElementById('pdf-input');

    const wordInput = document.getElementById('word-input');

    const contentInput = document.getElementById('content-input');

    const form = document.getElementById('lecture-form');

    const radios = document.querySelectorAll(
        'input[name="content_source_radio"]'
    );

    const hiddenSource = document.getElementById(
        'content-source-hidden'
    );

    function toggleBlocks() {

        const source = document.querySelector(
            'input[name="content_source_radio"]:checked'
        ).value;

        hiddenSource.value = source;

        manualBlock.style.display =
            source === 'manual' ? 'block' : 'none';

        pdfBlock.style.display =
            source === 'pdf' ? 'block' : 'none';

        wordBlock.style.display =
            source === 'word' ? 'block' : 'none';

        pdfInput.required = source === 'pdf';

        wordInput.required = source === 'word';
    }

    radios.forEach(r =>
        r.addEventListener('change', toggleBlocks)
    );

    toggleBlocks();

    form.addEventListener('submit', function(e) {

        const source = document.querySelector(
            'input[name="content_source_radio"]:checked'
        ).value;

        if (source === 'manual') {

            pdfInput.removeAttribute('required');

            wordInput.removeAttribute('required');

            if (
                !contentInput.value ||
                contentInput.value === '<br>'
            ) {

                e.preventDefault();

                alert('Введите текст лекции.');

                return false;
            }
        }

        if (
            source === 'pdf' &&
            (!pdfInput.files || pdfInput.files.length === 0)
        ) {

            e.preventDefault();

            alert('Загрузите PDF файл.');

            return false;
        }

        if (
            source === 'word' &&
            (!wordInput.files || wordInput.files.length === 0)
        ) {

            e.preventDefault();

            alert('Загрузите Word файл.');

            return false;
        }
    });
});
</script>

</body>
</html>
