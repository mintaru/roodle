<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Редактировать курс</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 p-8">

    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <div class="mb-4">
            <x-back-button :url="route('courses.show', $course)" text="К курсу" />
        </div>
        <h1 class="text-2xl font-bold mb-4">Редактировать курс</h1>

        @if ($errors->any())
            <div class="p-3 bg-red-200 text-red-800 rounded mb-4">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('courses.update', $course) }}" method="POST" enctype="multipart/form-data"
            class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-medium">Название курса *</label>
                <input type="text" name="title" value="{{ old('title', $course->title) }}"
                    class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block font-medium">Текущее изображение</label>
                @if ($course->image_path)
                    <img src="{{ asset('storage/' . $course->image_path) }}" alt=""
                        class="w-24 h-24 object-cover mb-2">
                @endif

                <!-- Превью паттерна -->
                <div id="pattern-preview"
                    class="w-full h-32 rounded mb-2 overflow-hidden bg-gray-100 flex items-center justify-center">
                    <canvas id="trianglify-canvas" class="w-full h-full" style="display:none"></canvas>
                    <span id="preview-placeholder" class="text-gray-400 text-sm">Паттерн появится здесь</span>
                </div>

                <div class="flex gap-2 mb-2">
                    <button type="button" id="generate-pattern"
                        class="bg-indigo-500 text-white px-3 py-1 rounded text-sm hover:bg-indigo-600">
                        🎲 Случайный паттерн
                    </button>
                    <button type="button" id="clear-pattern"
                        class="bg-gray-300 text-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-400"
                        style="display:none">
                        ✕ Убрать
                    </button>
                </div>

                <input type="file" name="image_path" id="image_input" class="w-full" accept="image/*">
                <!-- Сюда будет подставлен паттерн как файл если юзер ничего не выбрал -->
                <input type="file" name="generated_pattern" id="generated_pattern_input" style="display:none">
                <input type="hidden" name="use_generated_pattern" id="use_generated_pattern" value="0">
            </div>

            <div>
                <label class="block font-medium">Доступен с:</label>
                <input type="datetime-local" name="period_start"
                    value="{{ old('period_start', $course->formatPeriodForInput('period_start')) }}"
                    class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block font-medium">Доступен до:</label>
                <input type="datetime-local" name="period_end"
                    value="{{ old('period_end', $course->formatPeriodForInput('period_end')) }}"
                    class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block font-medium mb-2">Доступен группам (можно задать своё время открытия/закрытия для
                    каждой группы):</label>
                <div class="space-y-4">
                    @foreach ($groups as $group)
                        @php
                            $pivot = $course->groups->firstWhere('id', $group->id)?->pivot;
                        @endphp
                        <div class="border rounded p-4 hover:bg-gray-50">
                            <label class="flex items-center space-x-2 mb-3">
                                <input type="checkbox" name="groups[]" value="{{ $group->id }}"
                                    class="group-checkbox text-blue-600 focus:ring-blue-500"
                                    @if ($course->groups->contains($group->id)) checked @endif>
                                <span class="font-medium">{{ $group->name }}</span>
                            </label>
                            <div class="ml-6 grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <label class="block text-gray-600 mb-1">Открыть с:</label>
                                    <input type="datetime-local" name="group_period_start[{{ $group->id }}]"
                                        value="{{ $pivot && $pivot->period_start ? \Carbon\Carbon::parse($pivot->period_start, 'UTC')->setTimezone('Asia/Krasnoyarsk')->format('Y-m-d\TH:i') : '' }}"
                                    onchange="checkPastDate(this)"
                                        class="w-full border rounded p-2 text-sm">
                                </div>
                                <div>
                                    <input type="datetime-local"
                                    name="group_period_end[{{ $group->id }}]"
                                    value="{{ $pivot && $pivot->period_end ? \Carbon\Carbon::parse($pivot->period_end, 'UTC')->setTimezone('Asia/Krasnoyarsk')->format('Y-m-d\TH:i') : '' }}"
                                    class="w-full border rounded p-2 text-sm">
                                </div>
                            </div>
                            <p class="ml-6 text-xs text-gray-500 mt-1">Оставьте пустым — будут использоваться общие даты
                                курса выше</p>
                        </div>
                    @endforeach
                </div>
            </div>


            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Обновить
            </button>
        </form>
    </div>

</body>
<script>
    function checkPastDate(input) {
        if (!input.value) return;

        const selected = new Date(input.value); // берём значение из datetime-local
        const now = new Date();

        if (selected < now) {
            alert('Нельзя выбрать дату в прошлом');
            input.value = ''; // очищаем, если нужно
        }
    }
    </script>

    <script src="https://unpkg.com/trianglify@4/dist/trianglify.bundle.js"></script>
    <script>
        (function() {
            const canvas = document.getElementById('trianglify-canvas');
            const placeholder = document.getElementById('preview-placeholder');
            const clearBtn = document.getElementById('clear-pattern');
            const useGeneratedInput = document.getElementById('use_generated_pattern');
            const imageInput = document.getElementById('image_input');
            const form = document.querySelector('form');
            let patternBlob = null;

            function randomColor() {
                const palettes = [
                    ['#6366f1', '#8b5cf6', '#a78bfa'],
                    ['#ec4899', '#f43f5e', '#fb7185'],
                    ['#f59e0b', '#f97316', '#fbbf24'],
                    ['#10b981', '#06b6d4', '#34d399'],
                    ['#3b82f6', '#6366f1', '#93c5fd'],
                    ['#14b8a6', '#06b6d4', '#67e8f9'],
                    ['#e0e7ff', '#c7d2fe', '#a5b4fc'],
                    ['#fce7f3', '#fbcfe8', '#f9a8d4'],
                    ['#fef3c7', '#fde68a', '#fcd34d'],
                    ['#d1fae5', '#a7f3d0', '#6ee7b7'],
                    ['#e0f2fe', '#bae6fd', '#7dd3fc'],
                    ['#ccfbf1', '#99f6e4', '#5eead4'],
                    ['#f3e8ff', '#e9d5ff', '#d8b4fe'],
                    ['#ffedd5', '#fed7aa', '#fdba74']
                ];
                return palettes[Math.floor(Math.random() * palettes.length)];
            }

            function drawPattern() {
                const colors = randomColor();
                const pattern = window.trianglify({
                    width: 400,
                    height: 200,
                    cellSize: Math.floor(Math.random() * 60) + 100,
                    xColors: colors,
                    yColors: 'match',
                });

                const c = pattern.toCanvas();
                c.style.width = '100%';
                c.style.height = '100%';

                const preview = document.getElementById('pattern-preview');
                preview.innerHTML = '';
                preview.appendChild(c);

                placeholder.style.display = 'none';
                clearBtn.style.display = 'inline';
                useGeneratedInput.value = '1';

                c.toBlob(blob => {
                    patternBlob = blob;
                }, 'image/png');
            }

            document.getElementById('generate-pattern').addEventListener('click', function() {
                drawPattern();
            });

            clearBtn.addEventListener('click', function() {
                const preview = document.getElementById('pattern-preview');
                preview.innerHTML = '<span id="preview-placeholder" class="text-gray-400 text-sm">Паттерн появится здесь</span>';
                clearBtn.style.display = 'none';
                useGeneratedInput.value = '0';
                patternBlob = null;
            });

            // Если выбрали файл вручную — убираем паттерн
            imageInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    clearBtn.style.display = 'none';
                    useGeneratedInput.value = '0';
                    patternBlob = null;
                }
            });

            // Перед отправкой формы — подставляем паттерн как файл
            form.addEventListener('submit', function(e) {
                if (useGeneratedInput.value === '1' && patternBlob && !imageInput.files.length) {
                    e.preventDefault();
                    const file = new File([patternBlob], 'pattern.png', {
                        type: 'image/png'
                    });
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    imageInput.files = dt.files;
                    form.submit();
                }
            });
        })();
    </script>
</html>
