<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Редактировать задание — {{ $assignment->title }}</title>

    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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

        <div style="padding: 0 0.75rem;">
            <p style="font-size: 13px; font-weight: 600; color: var(--gray-800); line-height: 1.4;">
                {{ $course->title }}
            </p>
        </div>

    </aside>

    {{-- Main --}}
    <main class="main">

        {{-- Breadcrumb --}}
        <nav style="display: flex; align-items: center; gap: 8px; margin-bottom: 1.75rem; font-size: 13px; color: var(--color-text-muted);">

            <a href="{{ route('home') }}"
               style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;"
               onmouseover="this.style.color='var(--teal-600)'"
               onmouseout="this.style.color='var(--color-text-muted)'">

                Курсы
            </a>

            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 18l6-6-6-6"/>
            </svg>

            <a href="{{ route('courses.show', $course) }}"
               style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;"
               onmouseover="this.style.color='var(--teal-600)'"
               onmouseout="this.style.color='var(--color-text-muted)'">

                {{ $course->title }}
            </a>

            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 18l6-6-6-6"/>
            </svg>

            <span style="color: var(--gray-600); font-weight: 500;">
                Редактировать задание
            </span>

        </nav>

        {{-- Page header --}}
        <div class="page-header">
            <h1 class="page-header__title">
                Редактировать задание
            </h1>
        </div>

        <div style="max-width: 760px;">

            {{-- Main card --}}
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

                    <span style="font-size: 13px; font-weight: 600; color: var(--teal-700);">
                        {{ $course->title }}
                    </span>

                </div>

                <form
                    action="{{ route('assignments.update', [$course, $assignment]) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    x-data="fileUpload()"
                >

                    @csrf
                    @method('PUT')

                    {{-- Validation errors --}}
                    @if ($errors->any())

                        <div style="
                            background: #ffebee;
                            border: 1px solid #ffcdd2;
                            border-radius: var(--r-md);
                            padding: 12px 16px;
                            margin-bottom: 1.5rem;
                        ">

                            <p style="
                                font-size: 13px;
                                font-weight: 600;
                                color: var(--red-500);
                                margin-bottom: 6px;
                            ">
                                Пожалуйста, исправьте ошибки:
                            </p>

                            <ul style="
                                list-style: none;
                                padding: 0;
                                margin: 0;
                                display: flex;
                                flex-direction: column;
                                gap: 4px;
                            ">

                                @foreach ($errors->all() as $error)

                                    <li style="font-size: 13px; color: var(--red-500);">
                                        • {{ $error }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif

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

                            Название задания
                            <span style="color: var(--red-400);">*</span>

                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            required
                            value="{{ old('title', $assignment->title) }}"
                            placeholder="Введите название задания"
                            style="
                                width: 100%;
                                padding: 10px 14px;
                                border: 1px solid var(--color-border);
                                border-radius: var(--r-md);
                                font-size: 14px;
                                font-family: var(--font-body);
                                color: var(--color-text-primary);
                                background: var(--color-surface);
                                transition: border-color 0.2s, box-shadow 0.2s;
                                outline: none;
                            "
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >

                    </div>

                    {{-- Description --}}
                    <div style="margin-bottom: 1.5rem;">

                        <label for="description"
                               style="
                                   display: block;
                                   font-size: 13px;
                                   font-weight: 600;
                                   color: var(--gray-700);
                                   margin-bottom: 8px;
                               ">

                            Описание

                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            placeholder="Краткое описание задания..."
                            style="
                                width: 100%;
                                padding: 12px 14px;
                                border: 1px solid var(--color-border);
                                border-radius: var(--r-md);
                                font-size: 14px;
                                font-family: var(--font-body);
                                color: var(--color-text-primary);
                                background: var(--color-surface);
                                transition: border-color 0.2s, box-shadow 0.2s;
                                outline: none;
                                resize: vertical;
                            "
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >{{ old('description', $assignment->description) }}</textarea>

                    </div>

                    {{-- Instructions --}}
                    <div style="margin-bottom: 1.5rem;">

                        <label for="instructions"
                               style="
                                   display: block;
                                   font-size: 13px;
                                   font-weight: 600;
                                   color: var(--gray-700);
                                   margin-bottom: 8px;
                               ">

                            Инструкции

                        </label>

                        <textarea
                            id="instructions"
                            name="instructions"
                            rows="6"
                            placeholder="Опишите, что должны сделать ученики..."
                            style="
                                width: 100%;
                                padding: 12px 14px;
                                border: 1px solid var(--color-border);
                                border-radius: var(--r-md);
                                font-size: 14px;
                                font-family: var(--font-body);
                                color: var(--color-text-primary);
                                background: var(--color-surface);
                                transition: border-color 0.2s, box-shadow 0.2s;
                                outline: none;
                                resize: vertical;
                            "
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >{{ old('instructions', $assignment->instructions) }}</textarea>

                    </div>

                    {{-- Due date --}}
                    <div style="margin-bottom: 2rem;">

                        <label for="due_date"
                               style="
                                   display: block;
                                   font-size: 13px;
                                   font-weight: 600;
                                   color: var(--gray-700);
                                   margin-bottom: 8px;
                               ">

                            Срок сдачи

                        </label>

                        <input
                            type="datetime-local"
                            id="due_date"
                            name="due_date"
                            value="{{ old('due_date', $assignment->due_date?->format('Y-m-d\TH:i')) }}"
                            style="
                                width: 100%;
                                padding: 10px 14px;
                                border: 1px solid var(--color-border);
                                border-radius: var(--r-md);
                                font-size: 14px;
                                font-family: var(--font-body);
                                color: var(--color-text-primary);
                                background: var(--color-surface);
                                transition: border-color 0.2s, box-shadow 0.2s;
                                outline: none;
                            "
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >

                    </div>

                    {{-- Existing files --}}
                    @if($assignment->files->count())

                        <div style="margin-bottom: 2rem;">

                            <label style="
                                display: block;
                                font-size: 13px;
                                font-weight: 600;
                                color: var(--gray-700);
                                margin-bottom: 10px;
                            ">
                                Текущие файлы
                            </label>

                            <div style="
                                display: flex;
                                flex-direction: column;
                                gap: 12px;
                            ">

                                @foreach($assignment->files as $file)

                                    <div style="
                                        display: flex;
                                        align-items: center;
                                        justify-content: space-between;
                                        gap: 16px;
                                        padding: 14px;
                                        border: 1px solid var(--color-border);
                                        border-radius: var(--r-lg);
                                        background: var(--gray-50);
                                    ">

                                        <div style="min-width: 0;">

                                            <p style="
                                                font-size: 14px;
                                                font-weight: 600;
                                                color: var(--gray-800);
                                                margin-bottom: 4px;
                                                word-break: break-word;
                                            ">
                                                {{ $file->title }}
                                            </p>

                                            <p style="
                                                font-size: 12px;
                                                color: var(--color-text-muted);
                                            ">
                                                {{ number_format($file->file_size / 1024, 2) }} KB
                                            </p>

                                        </div>

                                        <form
                                            action="{{ route('assignments.delete-file', [$course, $assignment, $file]) }}"
                                            method="POST"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                onclick="return confirm('Удалить файл?')"
                                                style="
                                                    padding: 8px 14px;
                                                    border: none;
                                                    border-radius: var(--r-full);
                                                    background: var(--red-500);
                                                    color: white;
                                                    font-size: 12px;
                                                    font-weight: 600;
                                                    cursor: pointer;
                                                    transition: opacity 0.2s;
                                                "
                                                onmouseover="this.style.opacity='0.9'"
                                                onmouseout="this.style.opacity='1'"
                                            >
                                                Удалить
                                            </button>

                                        </form>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    @endif

                    {{-- Upload new files --}}
                    <div style="margin-bottom: 2rem;">

                        <label style="
                            display: block;
                            font-size: 13px;
                            font-weight: 600;
                            color: var(--gray-700);
                            margin-bottom: 8px;
                        ">
                            Добавить новые файлы
                        </label>

                        <div
                            x-on:dragover.prevent="dragging = true"
                            x-on:dragleave.prevent="dragging = false"
                            x-on:drop.prevent="handleDrop($event)"
                            x-on:click="$refs.fileInput.click()"
                            :style="dragging ? 'border-color: var(--teal-400); background: var(--teal-50);' : ''"
                            style="
                                border: 2px dashed var(--color-border-2);
                                border-radius: var(--r-lg);
                                padding: 2rem 1.5rem;
                                text-align: center;
                                cursor: pointer;
                                transition: border-color 0.2s, background 0.2s;
                            "
                        >

                            <input
                                type="file"
                                id="files"
                                name="files[]"
                                multiple
                                x-ref="fileInput"
                                x-on:change="handleFiles($event)"
                                style="display: none;"
                            >

                            <template x-if="files.length === 0">

                                <div>

                                    <div style="
                                        width: 48px;
                                        height: 48px;
                                        border-radius: var(--r-md);
                                        background: var(--gray-100);
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        margin: 0 auto 1rem;
                                    ">

                                        <svg width="22"
                                             height="22"
                                             fill="none"
                                             stroke="var(--gray-400)"
                                             stroke-width="1.5"
                                             viewBox="0 0 24 24">

                                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                            <polyline points="17 8 12 3 7 8"/>
                                            <line x1="12" y1="3" x2="12" y2="15"/>

                                        </svg>

                                    </div>

                                    <p style="
                                        font-size: 14px;
                                        font-weight: 600;
                                        color: var(--gray-700);
                                        margin-bottom: 4px;
                                    ">
                                        Перетащите файлы сюда
                                    </p>

                                    <p style="
                                        font-size: 13px;
                                        color: var(--color-text-muted);
                                    ">
                                        или
                                        <span style="color: var(--teal-600); font-weight: 600;">
                                            выберите с устройства
                                        </span>
                                    </p>

                                    <p style="
                                        font-size: 12px;
                                        color: var(--color-text-muted);
                                        margin-top: 12px;
                                    ">
                                        Можно выбрать несколько файлов · до 100 МБ каждый
                                    </p>

                                </div>

                            </template>

                            <template x-if="files.length > 0">

                                <div style="
                                    display: flex;
                                    flex-direction: column;
                                    gap: 10px;
                                    text-align: left;
                                ">

                                    <template x-for="file in files" :key="file.name">

                                        <div style="
                                            display: flex;
                                            align-items: center;
                                            gap: 12px;
                                            padding: 10px 12px;
                                            background: white;
                                            border-radius: var(--r-md);
                                            border: 1px solid var(--color-border);
                                        ">

                                            <div style="
                                                width: 40px;
                                                height: 40px;
                                                border-radius: var(--r-md);
                                                background: var(--teal-50);
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                flex-shrink: 0;
                                            ">

                                                <svg width="18"
                                                     height="18"
                                                     fill="none"
                                                     stroke="var(--teal-600)"
                                                     stroke-width="1.5"
                                                     viewBox="0 0 24 24">

                                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                                    <polyline points="14 2 14 8 20 8"/>

                                                </svg>

                                            </div>

                                            <div style="flex: 1; min-width: 0;">

                                                <p style="
                                                    font-size: 14px;
                                                    font-weight: 600;
                                                    color: var(--gray-800);
                                                    white-space: nowrap;
                                                    overflow: hidden;
                                                    text-overflow: ellipsis;
                                                " x-text="file.name"></p>

                                                <p style="
                                                    font-size: 12px;
                                                    color: var(--color-text-muted);
                                                " x-text="file.size"></p>

                                            </div>

                                        </div>

                                    </template>

                                </div>

                            </template>

                        </div>

                    </div>

                    {{-- Actions --}}
                    <div style="
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        flex-wrap: wrap;
                    ">

                        <button type="submit"
                                class="btn btn-primary"
                                style="padding: 10px 24px;">

                            <svg width="16"
                                 height="16"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 viewBox="0 0 24 24">

                                <path d="M5 13l4 4L19 7"/>

                            </svg>

                            Сохранить изменения

                        </button>

                        <a href="{{ route('courses.show', $course) }}"
                           class="btn btn-ghost">

                            Отмена

                        </a>

                    </div>

                </form>

            </div>

            {{-- Hint --}}
            <div style="
                display: flex;
                align-items: flex-start;
                gap: 10px;
                margin-top: 1rem;
                padding: 12px 14px;
                background: var(--sky-50);
                border-radius: var(--r-md);
                border: 1px solid var(--sky-100);
            ">

                <svg width="16"
                     height="16"
                     fill="none"
                     stroke="var(--sky-500)"
                     stroke-width="2"
                     viewBox="0 0 24 24"
                     style="flex-shrink: 0; margin-top: 1px;">

                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>

                </svg>

                <p style="
                    font-size: 13px;
                    color: var(--sky-700);
                    line-height: 1.5;
                ">
                    После сохранения обновлённое задание сразу станет доступно студентам.
                </p>

            </div>

        </div>

    </main>

</div>

<script>
function fileUpload() {
    return {
        dragging: false,
        files: [],

        handleFiles(event) {
            const selectedFiles = Array.from(event.target.files);

            this.files = selectedFiles.map(file => ({
                name: file.name,
                size: this.formatSize(file.size)
            }));
        },

        handleDrop(event) {
            this.dragging = false;

            const droppedFiles = event.dataTransfer.files;

            if (droppedFiles.length) {
                this.$refs.fileInput.files = droppedFiles;

                this.files = Array.from(droppedFiles).map(file => ({
                    name: file.name,
                    size: this.formatSize(file.size)
                }));
            }
        },

        formatSize(bytes) {
            const mb = bytes / (1024 * 1024);

            return mb < 1
                ? (bytes / 1024).toFixed(1) + ' КБ'
                : mb.toFixed(1) + ' МБ';
        }
    }
}
</script>

</body>
</html>
