{{-- Меню --}}
@include('components.menu')
    <div class="mb-4">
        <x-back-button :url="route('courses.show', $course)" text="К курсу" />
    </div>
    <h1>Добавить лекцию к курсу: {{ $course->title }}</h1>
    <link rel="stylesheet" href="{{ asset('css/lecture-create.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.min.css" rel="stylesheet">
    
    <style>
        .trix-color-toolbar {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 6px 10px;
            background: #f8f8f8;
            border: 1px solid #bbb;
            border-bottom: none;
            border-radius: 4px 4px 0 0;
            flex-wrap: wrap;
        }

        .color-swatch {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #aaa;
            cursor: pointer;
            transition: transform 0.1s;
            padding: 0;
        }

        .color-swatch:hover {
            transform: scale(1.25);
        }

        .color-swatch.active {
            box-shadow: 0 0 0 2px #333;
            transform: scale(1.2);
        }

        #reset-color {
            padding: 2px 7px;
            cursor: pointer;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #fff;
        }

        #custom-color {
            width: 28px;
            height: 28px;
            padding: 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>

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
                <label class="tab-option">
                    <input type="radio" name="content_source_radio" value="word">
                    <span>Загрузить Word</span>
                </label>
            </div>
        </div>

        <div id="manual-content-block">
            <label>Текст лекции</label>
            
            {{-- Панель выбора цвета --}}
            <div class="trix-color-toolbar" id="trix-color-toolbar">
                <span style="font-size: 13px; margin-right: 6px;">Цвет текста:</span>
                @foreach(['e74c3c','e67e22','f1c40f','2ecc71','3498db','9b59b6','000000','ffffff'] as $color)
                    <button 
                        type="button"
                        class="color-swatch" 
                        data-color="#{{ $color }}"
                        style="background: #{{ $color }};"
                        title="#{{ $color }}"
                    ></button>
                @endforeach
                <button type="button" id="reset-color" title="Сбросить цвет">✕</button>
                <input type="color" id="custom-color" title="Свой цвет">
            </div>
            
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

        <div id="word-block" style="display: none;">
            <label>Word файл</label>
            <input type="file" name="word" id="word-input" accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
            <small style="display: block; color: #666; margin-top: 8px;">Поддерживаются форматы .doc и .docx. Форматирование будет сохранено.</small>
        </div>

        <button type="submit">Создать лекцию</button>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.umd.min.js"></script>
    <script>
        document.addEventListener('trix-initialize', function () {
            const editor = document.querySelector('trix-editor.trix-editor-lecture');
            const COLORS = [
                '#e74c3c', '#e67e22', '#f1c40f', '#2ecc71',
                '#3498db', '#9b59b6', '#000000', '#ffffff'
            ];

            // Регистрируем атрибуты цвета после инициализации Trix
            COLORS.forEach(color => {
                const key = 'color' + color.replace('#', '');
                Trix.config.textAttributes[key] = {
                    style: { color },
                    inheritable: true,
                    parser: el => el.style.color === color
                };
            });

            function clearColors() {
                COLORS.forEach(color => {
                    editor.editor.deactivateAttribute('color' + color.replace('#', ''));
                });
                document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
            }

            function applyColor(color) {
                clearColors();
                editor.editor.activateAttribute('color' + color.replace('#', ''));
                // Подсветить активную кнопку
                const btn = document.querySelector(`.color-swatch[data-color="${color}"]`);
                if (btn) btn.classList.add('active');
            }

            // Клик по свотчам
            document.querySelectorAll('.color-swatch').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    applyColor(btn.dataset.color);
                });
            });

            // Сброс цвета
            document.getElementById('reset-color').addEventListener('click', (e) => {
                e.preventDefault();
                clearColors();
            });

            // Произвольный цвет
            const customPicker = document.getElementById('custom-color');
            customPicker.addEventListener('input', (e) => {
                const hex = e.target.value;
                // Регистрируем новый атрибут на лету, если его ещё нет
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

            // Обработчик загрузки изображений - перехватываем в момент добавления файла
            document.addEventListener('trix-file-accept', function(e) {
                // Разрешаем только изображения
                if (!e.file.type.startsWith('image/')) {
                    return;
                }

                // Предотвращаем стандартное действие (загрузку как base64)
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
                        console.log('Image URL:', result.url);
                        // Вставляем изображение напрямую как HTML
                        editor.editor.insertHTML('<figure class="attachment attachment--preview attachment--image"><img src="' + result.url + '" alt="' + e.file.name + '" /></figure>');
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    alert('Ошибка при загрузке изображения');
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const manualBlock = document.getElementById('manual-content-block');
            const pdfBlock = document.getElementById('pdf-block');
            const wordBlock = document.getElementById('word-block');
            const pdfInput = document.getElementById('pdf-input');
            const wordInput = document.getElementById('word-input');
            const contentInput = document.getElementById('content-input');
            const form = document.getElementById('lecture-form');
            const radios = document.querySelectorAll('input[name="content_source_radio"]');
            const hiddenSource = document.getElementById('content-source-hidden');

            function toggleBlocks() {
                const source = document.querySelector('input[name="content_source_radio"]:checked').value;
                const isManual = source === 'manual';
                const isPdf = source === 'pdf';
                const isWord = source === 'word';
                hiddenSource.value = source;
                manualBlock.style.display = isManual ? 'block' : 'none';
                pdfBlock.style.display = isPdf ? 'block' : 'none';
                wordBlock.style.display = isWord ? 'block' : 'none';
                pdfInput.required = isPdf;
                wordInput.required = isWord;
            }

            radios.forEach(r => r.addEventListener('change', toggleBlocks));
            toggleBlocks();

            form.addEventListener('submit', function(e) {
                const source = document.querySelector('input[name="content_source_radio"]:checked').value;
                const isManual = source === 'manual';
                const isPdf = source === 'pdf';
                const isWord = source === 'word';
                
                if (isManual) {
                    pdfInput.removeAttribute('required');
                    wordInput.removeAttribute('required');
                    if (!contentInput.value || contentInput.value === '<br>') {
                        e.preventDefault();
                        alert('Введите текст лекции или загрузите PDF файл.');
                        return false;
                    }
                } else if (isPdf) {
                    if (!pdfInput.files || pdfInput.files.length === 0) {
                        e.preventDefault();
                        alert('Загрузите PDF файл или введите текст вручную.');
                        return false;
                    }
                } else if (isWord) {
                    if (!wordInput.files || wordInput.files.length === 0) {
                        e.preventDefault();
                        alert('Загрузите Word файл или введите текст вручную.');
                        return false;
                    }
                }
            });
        });
    </script>
