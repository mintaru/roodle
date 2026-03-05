<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование лекции</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <div class="mb-4">
        <x-back-button :url="route('lectures.show', ['course' => $lecture->course, 'lecture' => $lecture])" text="К лекции" />
    </div>
    <h1 class="text-2xl font-bold mb-6">Редактирование лекции</h1>

    @if($errors->any())
        <div class="p-3 bg-red-200 text-red-800 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.lectures.update', $lecture) }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="lecture-edit-form">
        @csrf
        @method('PUT')
        <input type="hidden" name="content_source" id="content-source-hidden" value="{{ ($lecture->content_type ?? 'text') === 'html' ? 'manual' : 'pdf' }}">

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Название лекции</label>
            <input type="text" id="title" name="title" value="{{ old('title', $lecture->title) }}" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Введите название лекции">
        </div>

        <div>
            <label for="course" class="block text-sm font-medium text-gray-700 mb-2">Курс</label>
            <input type="text" id="course" disabled value="{{ $lecture->course->title }}" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-gray-500">
            <p class="text-sm text-gray-500 mt-1">Курс не может быть изменен</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Способ контента</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="content_source_radio" value="manual" {{ ($lecture->content_type ?? 'text') === 'html' ? 'checked' : '' }}>
                    <span>Текст вручную</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="content_source_radio" value="pdf" {{ ($lecture->content_type ?? 'text') !== 'html' && !$lecture->pdf_path ? '' : (($lecture->content_type ?? 'text') !== 'html' ? 'checked' : '') }}>
                    <span>PDF файл</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="content_source_radio" value="word">
                    <span>Word файл</span>
                </label>
            </div>
        </div>

        <div id="manual-content-block" style="{{ ($lecture->content_type ?? 'text') !== 'html' ? 'display: none;' : '' }}">
            <label for="content-input" class="block text-sm font-medium text-gray-700 mb-2">Текст лекции</label>
            
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
            
            <input id="content-input" type="hidden" name="content" value="{{ old('content', $lecture->content ?? '') }}">
            <trix-editor input="content-input" class="min-h-[300px] border border-gray-300 rounded p-3"></trix-editor>
        </div>

        <div id="pdf-block" style="{{ ($lecture->content_type ?? 'text') === 'html' ? 'display: none;' : '' }}">
            <label for="pdf" class="block text-sm font-medium text-gray-700 mb-2">PDF файл</label>
            <p class="text-sm text-gray-600 mb-2">
                @if($lecture->pdf_path)
                    Текущий файл: <a href="{{ asset('storage/' . $lecture->pdf_path) }}" target="_blank" class="text-blue-600 hover:underline">Скачать</a>
                @else
                    Файл не загружен
                @endif
            </p>
            <input type="file" id="pdf" name="pdf" accept=".pdf" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="from_page" class="block text-sm font-medium text-gray-700 mb-2">С какой страницы</label>
                    <input type="number" id="from_page" name="from_page" min="1" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="1">
                </div>
                <div>
                    <label for="to_page" class="block text-sm font-medium text-gray-700 mb-2">До какой страницы</label>
                    <input type="number" id="to_page" name="to_page" min="1" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Последняя">
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-1">Параметры страниц применяются только при загрузке нового PDF</p>
        </div>

        <div id="word-block" style="display: none;">
            <label for="word" class="block text-sm font-medium text-gray-700 mb-2">Word файл</label>
            <input type="file" id="word" name="word" accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-sm text-gray-500 mt-1">Поддерживаются форматы .doc и .docx. Форматирование будет сохранено.</p>
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Сохранить изменения</button>
            <a href="{{ route('admin.lectures.index') }}" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Отмена</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.umd.min.js"></script>
<script>
    // Извлекаем все цвета из сохраненного контента перед инициализацией
    function extractColorsFromContent(html) {
        const colors = new Set();
        const regex = /style="[^"]*color:\s*([^;}"]+)[^"]*"/gi;
        let match;
        while ((match = regex.exec(html)) !== null) {
            const color = match[1].trim();
            colors.add(color);
        }
        return Array.from(colors);
    }

    const contentInput = document.getElementById('content-input');
    const savedContent = contentInput ? contentInput.value : '';
    const colorsFromContent = extractColorsFromContent(savedContent);

    document.addEventListener('trix-initialize', function (event) {
        const editor = event.target;
        const COLORS = [
            '#e74c3c', '#e67e22', '#f1c40f', '#2ecc71',
            '#3498db', '#9b59b6', '#000000', '#ffffff'
        ];

        // Объединяем стандартные цвета с цветами из контента
        const allColors = new Set([...COLORS, ...colorsFromContent]);

        console.log('Colors from content:', colorsFromContent);
        console.log('All colors to register:', Array.from(allColors));

        // Функция для нормализации цветов (rgb -> hex, etc)
        function normalizeColor(color) {
            if (!color) return '';
            color = color.trim().toLowerCase();
            
            // Если уже hex
            if (color.startsWith('#')) return color;
            
            // Преобразуем rgb/rgba в hex
            if (color.startsWith('rgb')) {
                const matches = color.match(/\d+/g);
                if (matches && matches.length >= 3) {
                    return '#' + matches.slice(0, 3).map(x => {
                        const num = parseInt(x);
                        return num.toString(16).padStart(2, '0');
                    }).join('');
                }
            }
            return color;
        }

        // Регистрируем атрибуты цвета
        allColors.forEach(color => {
            const key = 'color' + color.replace(/[^a-z0-9]/gi, '');
            Trix.config.textAttributes[key] = {
                style: { color },
                inheritable: true,
                parser: el => {
                    if (!el.style.color) return false;
                    const normalized = normalizeColor(el.style.color);
                    const normalizedColor = normalizeColor(color);
                    return normalized === normalizedColor;
                }
            };
        });

        // Восстанавливаем атрибуты цвета из уже загруженного HTML
        setTimeout(() => {
            const editorElement = editor.editor;
            if (editorElement && editorElement.document) {
                const document = editorElement.document;
                
                // Проходим по всем атрибутам в документе и восстанавливаем цвета
                document.getBlocks().forEach(block => {
                    block.getAttributeRanges().forEach(range => {
                        // Атрибуты уже должны быть восстановлены парсером
                    });
                });
            }
        }, 100);

        function clearColors() {
            allColors.forEach(color => {
                const key = 'color' + color.replace(/[^a-z0-9]/gi, '');
                editor.editor.deactivateAttribute(key);
            });
            document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
        }

        function applyColor(color) {
            clearColors();
            const key = 'color' + color.replace(/[^a-z0-9]/gi, '');
            editor.editor.activateAttribute(key);
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
            const key = 'color' + hex.replace(/[^a-z0-9]/gi, '');
            if (!Trix.config.textAttributes[key]) {
                Trix.config.textAttributes[key] = {
                    style: { color: hex },
                    inheritable: true,
                    parser: el => {
                        if (!el.style.color) return false;
                        return normalizeColor(el.style.color) === normalizeColor(hex);
                    }
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
    }, false);

    document.addEventListener('DOMContentLoaded', function() {
        const manualBlock = document.getElementById('manual-content-block');
        const pdfBlock = document.getElementById('pdf-block');
        const wordBlock = document.getElementById('word-block');
        const hiddenSource = document.getElementById('content-source-hidden');
        const radios = document.querySelectorAll('input[name="content_source_radio"]');

        function toggleBlocks() {
            const source = document.querySelector('input[name="content_source_radio"]:checked').value;
            hiddenSource.value = source;
            manualBlock.style.display = source === 'manual' ? 'block' : 'none';
            pdfBlock.style.display = source === 'pdf' ? 'block' : 'none';
            wordBlock.style.display = source === 'word' ? 'block' : 'none';
        }
        radios.forEach(r => r.addEventListener('change', toggleBlocks));
    });
</script>
</body>
</html>
