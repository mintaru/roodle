<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Добавить материал — {{ $course->title }}</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>

{{-- Меню --}}
@include('components.menu')

<div class="layout">
    {{-- Sidebar --}}
    <aside class="sidebar">
        <p class="sidebar-section-title">Навигация</p>
        <a href="{{ route('courses.show', $course) }}" class="sidebar-link">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            К курсу
        </a>
        <a href="{{ route('home') }}" class="sidebar-link">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Все курсы
        </a>

        <p class="sidebar-section-title" style="margin-top: 2rem;">Курс</p>
        <div style="padding: 0 0.75rem;">
            <p style="font-size: 13px; font-weight: 600; color: var(--gray-800); line-height: 1.4;">{{ $course->title }}</p>
        </div>
    </aside>

    {{-- Main --}}
    <main class="main">

        {{-- Breadcrumb --}}
        <nav style="display: flex; align-items: center; gap: 8px; margin-bottom: 1.75rem; font-size: 13px; color: var(--color-text-muted);">
            <a href="{{ route('home') }}" style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">Курсы</a>
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <a href="{{ route('courses.show', $course) }}" style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">{{ $course->title }}</a>
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <span style="color: var(--gray-600); font-weight: 500;">Добавить материал</span>
        </nav>

        {{-- Page header --}}
        <div class="page-header">
            <p class="page-header__greeting">Загрузка файла</p>
            <h1 class="page-header__title">Добавить материал</h1>
        </div>

        {{-- Form card --}}
        <div style="max-width: 600px;">
            <div class="panel" style="padding: 2rem;">

                {{-- Course badge --}}
                <div style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; background: var(--teal-50); border-radius: var(--r-md); margin-bottom: 2rem; border: 1px solid var(--teal-100);">
                    <svg width="16" height="16" fill="none" stroke="var(--teal-600)" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
                    <span style="font-size: 13px; font-weight: 600; color: var(--teal-700);">{{ $course->title }}</span>
                </div>

                <form
                    action="{{ route('materials.store', $course) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    x-data="fileUpload()"
                >
                    @csrf

                    {{-- Validation errors --}}
                    @if ($errors->any())
                        <div style="background: #ffebee; border: 1px solid #ffcdd2; border-radius: var(--r-md); padding: 12px 16px; margin-bottom: 1.5rem;">
                            <p style="font-size: 13px; font-weight: 600; color: var(--red-500); margin-bottom: 6px;">Пожалуйста, исправьте ошибки:</p>
                            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 4px;">
                                @foreach ($errors->all() as $error)
                                    <li style="font-size: 13px; color: var(--red-500);">• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Title --}}
                    <div style="margin-bottom: 1.5rem;">
                        <label for="title" style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">
                            Название материала <span style="color: var(--red-400);">*</span>
                        </label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            required
                            placeholder="Например: Конспект лекции №3"
                            value="{{ old('title') }}"
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
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0, 181, 165, 0.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >
                    </div>

                    {{-- File upload --}}
                    <div style="margin-bottom: 2rem;">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">
                            Файл <span style="color: var(--red-400);">*</span>
                        </label>

                        {{-- Drop zone --}}
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
                            onmouseover="if(!this.querySelector('[x-bind]')) { this.style.borderColor='var(--teal-300)'; this.style.background='var(--gray-50)'; }"
                            onmouseout="this.style.borderColor=''; this.style.background='';"
                        >
                            <input
                                type="file"
                                id="file"
                                name="file"
                                required
                                x-ref="fileInput"
                                x-on:change="handleFile($event)"
                                style="display: none;"
                            >

                            {{-- Empty state --}}
                            <template x-if="!fileName">
                                <div>
                                    <div style="width: 48px; height: 48px; border-radius: var(--r-md); background: var(--gray-100); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                        <svg width="22" height="22" fill="none" stroke="var(--gray-400)" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                            <polyline points="17 8 12 3 7 8"/>
                                            <line x1="12" y1="3" x2="12" y2="15"/>
                                        </svg>
                                    </div>
                                    <p style="font-size: 14px; font-weight: 600; color: var(--gray-700); margin-bottom: 4px;">Перетащите файл сюда</p>
                                    <p style="font-size: 13px; color: var(--color-text-muted);">или <span style="color: var(--teal-600); font-weight: 600;">выберите с устройства</span></p>
                                    <p style="font-size: 12px; color: var(--color-text-muted); margin-top: 12px;">PDF, Word, Excel, PowerPoint и другие · до 100 МБ</p>
                                </div>
                            </template>

                            {{-- File selected state --}}
                            <template x-if="fileName">
                                <div style="display: flex; align-items: center; gap: 12px; text-align: left;">
                                    <div style="width: 44px; height: 44px; border-radius: var(--r-md); background: var(--teal-50); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <svg width="20" height="20" fill="none" stroke="var(--teal-600)" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                        </svg>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <p style="font-size: 14px; font-weight: 600; color: var(--gray-800); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" x-text="fileName"></p>
                                        <p style="font-size: 12px; color: var(--color-text-muted);" x-text="fileSize"></p>
                                    </div>
                                    <button
                                        type="button"
                                        x-on:click.stop="clearFile()"
                                        style="padding: 6px; border: none; background: var(--gray-100); border-radius: var(--r-sm); cursor: pointer; color: var(--gray-500); transition: background 0.2s;"
                                        onmouseover="this.style.background='var(--gray-200)'"
                                        onmouseout="this.style.background='var(--gray-100)'"
                                    >
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            Загрузить материал
                        </button>
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-ghost">
                            Отмена
                        </a>
                    </div>
                </form>
            </div>

            {{-- Hint --}}
            <div style="display: flex; align-items: flex-start; gap: 10px; margin-top: 1rem; padding: 12px 14px; background: var(--sky-50); border-radius: var(--r-md); border: 1px solid var(--sky-100);">
                <svg width="16" height="16" fill="none" stroke="var(--sky-500)" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <p style="font-size: 13px; color: var(--sky-700); line-height: 1.5;">Материал станет доступен студентам сразу после загрузки. Поддерживаемые форматы: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX и текстовые файлы.</p>
            </div>
        </div>

    </main>
</div>

<script>
function fileUpload() {
    return {
        dragging: false,
        fileName: null,
        fileSize: null,
        handleFile(event) {
            const file = event.target.files[0];
            if (file) this.setFile(file);
        },
        handleDrop(event) {
            this.dragging = false;
            const file = event.dataTransfer.files[0];
            if (file) {
                this.$refs.fileInput.files = event.dataTransfer.files;
                this.setFile(file);
            }
        },
        setFile(file) {
            this.fileName = file.name;
            const mb = file.size / (1024 * 1024);
            this.fileSize = mb < 1
                ? (file.size / 1024).toFixed(1) + ' КБ'
                : mb.toFixed(1) + ' МБ';
        },
        clearFile() {
            this.fileName = null;
            this.fileSize = null;
            this.$refs.fileInput.value = '';
        }
    }
}
</script>

</body>
</html>
