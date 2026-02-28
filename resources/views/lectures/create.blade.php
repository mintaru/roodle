{{-- Меню --}}
@include('components.menu')
    <div class="mb-4">
        <x-back-button :url="route('courses.show', $course)" text="К курсу" />
    </div>
    <h1>Добавить лекцию к курсу: {{ $course->title }}</h1>
    <link rel="stylesheet" href="{{ asset('css/lecture-create.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.min.css" rel="stylesheet">
    <form action="{{ route('lectures.store', $course) }}" method="POST" enctype="multipart/form-data" id="lecture-form">
        @csrf
        <input type="hidden" name="content_source" id="content-source-hidden" value="manual">
        <div>
            <label>Название</label>
            <input type="text" name="title" required>
        </div>

        <div>
            <label>Способ добавления контента</label>
            <div class="content-source-tabs">
                <label class="tab-option">
                    <input type="radio" name="content_source_radio" value="manual" checked>
                    <span>Ввести текст вручную</span>
                </label>
                <label class="tab-option">
                    <input type="radio" name="content_source_radio" value="pdf">
                    <span>Загрузить PDF</span>
                </label>
            </div>
        </div>

        <div id="manual-content-block">
            <label>Текст лекции</label>
            <input id="content-input" type="hidden" name="content">
            <trix-editor input="content-input" class="trix-editor-lecture"></trix-editor>
        </div>

        <div id="pdf-block" style="display: none;">
            <label>PDF файл</label>
            <input type="file" name="pdf" id="pdf-input" accept="application/pdf">
            <div>
                <label>С какой страницы</label>
                <input type="number" name="from_page" min="1">
            </div>
            <div>
                <label>По какую страницу</label>
                <input type="number" name="to_page" min="1">
            </div>
        </div>

        <button type="submit">Создать лекцию</button>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const manualBlock = document.getElementById('manual-content-block');
            const pdfBlock = document.getElementById('pdf-block');
            const pdfInput = document.getElementById('pdf-input');
            const contentInput = document.getElementById('content-input');
            const form = document.getElementById('lecture-form');
            const radios = document.querySelectorAll('input[name="content_source_radio"]');
            const hiddenSource = document.getElementById('content-source-hidden');

            function toggleBlocks() {
                const source = document.querySelector('input[name="content_source_radio"]:checked').value;
                const isManual = source === 'manual';
                hiddenSource.value = source;
                manualBlock.style.display = isManual ? 'block' : 'none';
                pdfBlock.style.display = isManual ? 'none' : 'block';
                pdfInput.required = !isManual;
            }

            radios.forEach(r => r.addEventListener('change', toggleBlocks));
            toggleBlocks();

            form.addEventListener('submit', function(e) {
                const isManual = document.querySelector('input[name="content_source_radio"]:checked').value === 'manual';
                if (isManual) {
                    pdfInput.removeAttribute('required');
                    if (!contentInput.value || contentInput.value === '<br>') {
                        e.preventDefault();
                        alert('Введите текст лекции или загрузите PDF файл.');
                        return false;
                    }
                } else {
                    if (!pdfInput.files || pdfInput.files.length === 0) {
                        e.preventDefault();
                        alert('Загрузите PDF файл или введите текст вручную.');
                        return false;
                    }
                }
            });
        });
    </script>
