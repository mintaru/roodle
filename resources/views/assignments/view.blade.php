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

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header__desc {
            font-size: 14px;
            color: var(--color-text-muted);
            margin-top: 0.5rem;
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

        .btn-sm {
            padding: 8px 14px;
            font-size: 12px;
        }

        .empty-state {
            color: var(--color-text-muted);
            font-size: 14px;
        }

        @media (max-width: 640px) {
            .page-header__title {
                font-size: 20px !important;
            }
            .panel {
                padding: 1rem 1.25rem !important;
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

            <div style="
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 1.25rem;
                padding: 12px 16px;
                background: var(--green-50);
                border-radius: var(--r-md);
                border: 1px solid var(--green-100);
                font-size: 14px;
                font-weight: 600;
                color: var(--green-700);
            ">

                {{ session('success') }}

            </div>

        @endif

        {{-- Page header --}}
        <div class="page-header">
            <h1 class="page-header__title">{{ $assignment->title }}</h1>
            @if($assignment->description)
                <p class="page-header__desc">{{ $assignment->description }}</p>
            @endif
        </div>

        <div style="max-width: 760px;">

            @if($assignment->due_date)
                <div class="panel" style="padding: 1.25rem 2rem; margin-bottom: 1.25rem;">

                    <div style="display: flex; align-items: center; justify-content: center; gap: 0.75rem;">
                        <svg width="16" height="16" fill="none" stroke="var(--color-text-muted)" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <span style="font-size: 14px; font-weight: 600; color: var(--gray-800);">
                            {{ $assignment->due_date->format('d.m.Y H:i') }}
                        </span>
                        <span style="font-size: 12px; color: var(--color-text-muted);">— срок сдачи</span>
                    </div>

                </div>
            @endif

            {{-- Instructions --}}
            @if($assignment->instructions)

                <div class="panel" style="padding: 1.5rem 2rem; margin-bottom: 1.25rem;">

                    <p style="font-size: 14px; font-weight: 700; color: var(--gray-700); margin-bottom: 0.75rem;">
                        Инструкции
                    </p>

                    <div style="font-size: 14px; color: var(--color-text-secondary); line-height: 1.7;">
                        {!! nl2br(e($assignment->instructions)) !!}
                    </div>

                </div>

            @endif

            {{-- Files --}}
            @if($assignment->files->count())

                <div class="panel" style="padding: 1.5rem 2rem; margin-bottom: 1.25rem;">

                    <p style="font-size: 14px; font-weight: 700; color: var(--gray-700); margin-bottom: 0.75rem;">
                        Файлы задания
                    </p>

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
                                       class="btn btn-ghost btn-sm">

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

        {{-- Student submission area --}}
        @unless(Auth::user()->hasAnyRole(['teacher', 'admin']))

            <div style="max-width: 760px; margin-top: 1.25rem;" x-data="{ editing: false }">

                @if($submission && $submission->submitted_at)

                    {{-- Already submitted --}}
                    <div x-show="!editing">
                        <div class="panel" style="padding: 1.5rem 2rem;">

                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 0.75rem;">
                                <p style="font-size: 14px; font-weight: 700; color: var(--gray-700); margin: 0;">
                                    Ваш ответ
                                </p>
                                @if($submission->score === null)
                                    <button type="button" class="btn btn-ghost btn-sm" @click="editing = true">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        Редактировать
                                    </button>
                                @endif
                            </div>

                            @if($submission->answer_text)
                                <div class="submission-answer">
                                    {!! nl2br(e($submission->answer_text)) !!}
                                </div>
                            @endif

                            @if($submission->files->count())
                                <div style="margin-top: 1rem;">
                                    <p style="font-size: 12px; font-weight: 600; color: var(--color-text-muted); margin-bottom: 0.5rem;">
                                        Прикреплённые файлы
                                    </p>
                                    <ul class="file-list">
                                        @foreach($submission->files as $file)
                                            <li class="file-item">
                                                <div class="file-item__top">
                                                    <div>
                                                        <div class="file-title">{{ $file->file_name }}</div>
                                                        <div class="file-meta">{{ number_format($file->file_size / 1024, 2) }} KB</div>
                                                    </div>
                                                    <a href="{{ route('assignments.download-submission-file', [$course, $assignment, $submission, $file]) }}"
                                                       class="btn btn-ghost btn-sm">Скачать</a>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($submission->submitted_at)
                                <p style="font-size: 12px; color: var(--color-text-muted); margin-top: 0.75rem;">
                                    Отправлено: {{ $submission->submitted_at->format('d.m.Y H:i') }}
                                </p>
                            @endif

                            @if($submission->score !== null)
                                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                                    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                                        <div class="grade-display">{{ $submission->score }}</div>
                                        <div>
                                            <p style="font-size: 13px; color: var(--color-text-muted);">Оценка</p>
                                            @if($submission->graded_at)
                                                <p style="font-size: 12px; color: var(--color-text-muted);">
                                                    Проверено: {{ $submission->graded_at->format('d.m.Y H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($submission->teacher_comment)
                                        <div class="teacher-comment">
                                            <p style="font-size: 12px; font-weight: 700; color: var(--amber-700); margin-bottom: 0.5rem;">
                                                Комментарий преподавателя
                                            </p>
                                            {!! nl2br(e($submission->teacher_comment)) !!}
                                        </div>
                                    @endif
                                </div>
                            @endif

                        </div>
                    </div>

                    @if($submission->score === null)
                        {{-- Edit form --}}
                        <div x-show="editing" x-cloak>
                            <div class="panel" style="padding: 1.5rem 2rem;">
                                <p style="font-size: 14px; font-weight: 700; color: var(--gray-700); margin-bottom: 1.25rem;">
                                    Редактировать ответ
                                </p>

                                <form action="{{ route('assignments.submit', [$course, $assignment]) }}"
                                      method="POST"
                                      enctype="multipart/form-data"
                                      x-data="fileUpload()">

                                    @csrf

                                    <div class="form-group">
                                        <label for="answer_text" class="form-label">Текст ответа</label>
                                        <textarea id="answer_text" name="answer_text" rows="6"
                                                  class="form-control"
                                                  placeholder="Напишите ваш ответ здесь...">{{ old('answer_text', $submission->answer_text) }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Прикрепить новые файлы (текущие сохранятся)</label>
                                        <div
                                            x-on:dragover.prevent="dragging = true"
                                            x-on:dragleave.prevent="dragging = false"
                                            x-on:drop.prevent="handleDrop($event)"
                                            x-on:click="$refs.fileInput.click()"
                                            :style="dragging ? 'border-color: var(--teal-400); background: var(--teal-50);' : ''"
                                            style="
                                                border: 2px dashed var(--color-border-2);
                                                border-radius: var(--r-lg);
                                                padding: 1.5rem 1rem;
                                                text-align: center;
                                                cursor: pointer;
                                                transition: border-color 0.2s, background 0.2s;
                                            "
                                        >
                                            <input type="file" id="files" name="files[]" multiple
                                                   x-ref="fileInput"
                                                   x-on:change="handleFiles($event)"
                                                   style="display: none;">

                                            <template x-if="files.length === 0">
                                                <div>
                                                    <p style="font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 4px;">
                                                        Перетащите файлы сюда
                                                    </p>
                                                    <p style="font-size: 12px; color: var(--color-text-muted);">
                                                        или <span style="color: var(--teal-600); font-weight: 600;">выберите с устройства</span>
                                                    </p>
                                                    <p style="font-size: 11px; color: var(--color-text-muted); margin-top: 8px;">
                                                        Можно выбрать несколько файлов · до 100 МБ каждый
                                                    </p>
                                                </div>
                                            </template>

                                            <template x-if="files.length > 0">
                                                <div style="display: flex; flex-direction: column; gap: 8px; text-align: left;">
                                                    <template x-for="file in files" :key="file.name">
                                                        <div style="display: flex; align-items: center; gap: 10px; padding: 8px 10px; background: white; border-radius: var(--r-md); border: 1px solid var(--color-border);">
                                                            <div style="flex: 1; min-width: 0;">
                                                                <p style="font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" x-text="file.name"></p>
                                                                <p style="font-size: 11px; color: var(--color-text-muted);" x-text="file.size"></p>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div style="display: flex; gap: 10px;">
                                        <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                                            Сохранить изменения
                                        </button>
                                        <button type="button" class="btn btn-ghost" @click="editing = false">
                                            Отмена
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    @endif

                @else

                    {{-- Submission form --}}
                    <div class="panel" style="padding: 1.5rem 2rem;">

                        <p style="font-size: 14px; font-weight: 700; color: var(--gray-700); margin-bottom: 1.25rem;">
                            Отправить ответ
                        </p>

                        <form action="{{ route('assignments.submit', [$course, $assignment]) }}"
                              method="POST"
                              enctype="multipart/form-data"
                              x-data="fileUpload()">

                            @csrf

                            <div class="form-group">
                                <label for="answer_text" class="form-label">Текст ответа</label>
                                <textarea id="answer_text" name="answer_text" rows="6"
                                          class="form-control"
                                          placeholder="Напишите ваш ответ здесь...">{{ old('answer_text') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Прикрепить файлы</label>
                                <div
                                    x-on:dragover.prevent="dragging = true"
                                    x-on:dragleave.prevent="dragging = false"
                                    x-on:drop.prevent="handleDrop($event)"
                                    x-on:click="$refs.fileInput.click()"
                                    :style="dragging ? 'border-color: var(--teal-400); background: var(--teal-50);' : ''"
                                    style="
                                        border: 2px dashed var(--color-border-2);
                                        border-radius: var(--r-lg);
                                        padding: 1.5rem 1rem;
                                        text-align: center;
                                        cursor: pointer;
                                        transition: border-color 0.2s, background 0.2s;
                                    "
                                >
                                    <input type="file" id="files" name="files[]" multiple
                                           x-ref="fileInput"
                                           x-on:change="handleFiles($event)"
                                           style="display: none;">

                                    <template x-if="files.length === 0">
                                        <div>
                                            <p style="font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 4px;">
                                                Перетащите файлы сюда
                                            </p>
                                            <p style="font-size: 12px; color: var(--color-text-muted);">
                                                или <span style="color: var(--teal-600); font-weight: 600;">выберите с устройства</span>
                                            </p>
                                            <p style="font-size: 11px; color: var(--color-text-muted); margin-top: 8px;">
                                                Можно выбрать несколько файлов · до 100 МБ каждый
                                            </p>
                                        </div>
                                    </template>

                                    <template x-if="files.length > 0">
                                        <div style="display: flex; flex-direction: column; gap: 8px; text-align: left;">
                                            <template x-for="file in files" :key="file.name">
                                                <div style="display: flex; align-items: center; gap: 10px; padding: 8px 10px; background: white; border-radius: var(--r-md); border: 1px solid var(--color-border);">
                                                    <div style="flex: 1; min-width: 0;">
                                                        <p style="font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" x-text="file.name"></p>
                                                        <p style="font-size: 11px; color: var(--color-text-muted);" x-text="file.size"></p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                                Отправить ответ
                            </button>

                        </form>

                    </div>

                @endif

            </div>

        @endunless

        {{-- Teacher view: all submissions --}}
        {{-- Teacher submissions moved to assignments.show --}}

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
