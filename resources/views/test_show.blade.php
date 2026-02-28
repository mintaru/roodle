<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $test->title }}</title>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="
    https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.min.css
    " rel="stylesheet">
</head>

<body>
    <header class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Логотип" class="logo">
        <button id="theme-toggle" class="theme-toggle-btn">🌙 Тёмная тема</button>
    </header>

    <div class="container-main">
        <div class="mb-4">
            <x-back-button :url="route('courses.show', $test->course)" text="К курсу" />
        </div>
        <!-- Список вопросов теста -->
        <div>
            <div class="card-title">
                <h2>{{ $test->title }}</h2>
                <p>{{ $test->description }}</p>
            </div>
            <div class="card">
                <h3 class="text-xl font-bold mb-4">Вопросы в тесте ({{ $test->questions->count() }})</h3>

                @if($test->display_mode === 'paged')
                    <p class="mb-3 text-sm text-gray-600">
                        Разбиение по страницам активно. Укажите номер страницы для каждого вопроса
                        (например, 1–5 на первой, 6–10 на второй и т.д.).
                    </p>
                    <form action="{{ route('tests.update_layout', $test) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        @forelse($test->questions as $question)
                            <div class="question-item">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;">
                                    <div style="flex: 1;">
                                        <p class="font-semibold">
                                            {{ $loop->iteration }}. {!! $question->question_text !!}
                                        </p>
                                        <ul class="options-list mt-2">
                                            @foreach ($question->options as $option)
                                                <li class="{{ $option->is_correct ? 'correct-answer' : '' }}">
                                                    {{ $option->option_text }}
                                                    @if ($option->is_correct)
                                                        <span class="correct-label">(Верный ответ)</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                                        <label class="text-sm text-gray-700">
                                            Страница
                                            <input
                                                type="number"
                                                name="pages[{{ $question->id }}]"
                                                min="1"
                                                value="{{ $question->pivot->page_number ?? 1 }}"
                                                class="w-20 px-2 py-1 border rounded-md text-sm">
                                        </label>
                                        <button
                                            type="submit"
                                            form="delete-question-{{ $question->id }}"
                                            class="delete-btn"
                                            onclick="return confirm('Вы уверены, что хотите удалить этот вопрос из теста?')"
                                            style="background-color: #dc2626; color: white; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; white-space: nowrap;">
                                            🗑️ Удалить
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="no-questions">В этом тесте еще нет вопросов.</p>
                        @endforelse

                        @if($test->questions->count() > 0)
                            <div class="mt-4">
                                <button type="submit" class="submit-btn">
                                    Сохранить разбиение по страницам
                                </button>
                            </div>
                        @endif
                    </form>
                @else
                    <div class="space-y-4">
                        @forelse($test->questions as $question)
                            <div class="question-item">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div style="flex: 1;">
                                        <p class="font-semibold">{{ $loop->iteration }}. {!! $question->question_text !!}</p>
                                        <ul class="options-list mt-2">
                                            @foreach ($question->options as $option)
                                                <li class="{{ $option->is_correct ? 'correct-answer' : '' }}">
                                                    {{ $option->option_text }}
                                                    @if ($option->is_correct)
                                                        <span class="correct-label">(Верный ответ)</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <form
                                        action="{{ route('tests.removeQuestion', ['test' => $test->id, 'question' => $question->id]) }}"
                                        method="POST" style="display: inline; margin-left: 10px;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-btn"
                                            onclick="return confirm('Вы уверены, что хотите удалить этот вопрос из теста?')"
                                            style="background-color: #dc2626; color: white; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; white-space: nowrap;">🗑️
                                            Удалить</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="no-questions">В этом тесте еще нет вопросов.</p>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>

        <!-- Форма добавления нового вопроса -->
        <div>
            <div class="card">
                <h3>Добавить новый вопрос</h3>

                @if (session('success'))
                    <div class="success-message">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                <form action="/tests/{{ $test->id }}/questions" method="POST" id="question-form">
                    @csrf

                    <!-- Вывод ошибок валидации -->
                    @if ($errors->any())
                        <div style="background-color: #fee; border: 1px solid #f00; color: #c00; padding: 15px; margin-bottom: 15px; border-radius: 4px;">
                            <strong>Ошибки валидации:</strong>
                            <ul style="margin: 10px 0 0 20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Текст вопроса -->
                    <div class="form-group">
                        <label for="question_text">Текст вопроса</label>
                        <input id="question_text" type="hidden" name="question_text">
                        <trix-editor input="question_text"></trix-editor>
                    </div>


                    <!-- Тип вопроса -->
                    <div class="form-group">
                        <label>Тип вопроса</label>
                        <select name="question_type" id="question_type" required>
                            <option value="single_choice">Один правильный ответ (радиокнопки)</option>
                            <option value="multiple_choice">Несколько правильных ответов (чекбоксы)</option>
                            <option value="short_answer">Текстовый ответ</option>
                            <option value="rich_text_answer">Развёрнутый ответ (форматирование)</option>
                        </select>
                    </div>

                    <!-- Варианты ответов (для выбора) -->
                    <div class="form-group" id="options-group">
                        <label>Варианты ответов</label>
                        <div id="options-container" class="options-container">
                            <div class="option-item">
                                <input type="radio" name="correct_option" value="0" required>
                                <input type="text" name="options[0]" placeholder="Вариант 1" required>
                            </div>
                            <div class="option-item">
                                <input type="radio" name="correct_option" value="1">
                                <input type="text" name="options[1]" placeholder="Вариант 2" required>
                            </div>
                        </div>
                        <button type="button" id="add-option" class="add-option-btn">+ Добавить вариант</button>
                    </div>

                    <!-- Правильные текстовые ответы (для текстовых вопросов) -->
                    <div class="form-group" id="text-answers-group" style="display: none;">
                        <label>Правильные ответы</label>
                        <div id="text-answers-container" class="text-answers-container">
                            <div class="text-answer-item">
                                <input type="text" name="correct_answers[0]" placeholder="Правильный ответ 1" required>
                            </div>
                        </div>
                        <button type="button" id="add-text-answer" class="add-option-btn">+ Добавить ответ</button>
                        <div style="margin-top: 10px;">
                            <label>
                                <input type="checkbox" name="case_insensitive" value="1" checked>
                                Игнорировать регистр и пробелы при проверке
                            </label>
                        </div>
                        <!-- Hidden input для отправки значения 0, если checkbox не отмечен -->
                        <input type="hidden" name="case_insensitive" value="0">
                    </div>

                    <button type="submit" class="submit-btn">Создать вопрос</button>
                </form>

                <hr>

                <!-- Добавление вопроса из банка -->
                <h3>Добавить вопрос из банка</h3>
                <form action="/tests/{{ $test->id }}/add-from-bank" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Выбрать вопрос</label>
                        <select name="question_id" required>
                            @foreach ($allQuestions as $question)
                                <option value="{{ $question->id }}">{!! $question->question_text !!}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="submit-btn">Добавить из банка</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Отдельные скрытые формы для удаления (чтобы не было вложенных form) --}}
    @foreach($test->questions as $question)
        <form
            id="delete-question-{{ $question->id }}"
            action="{{ route('tests.removeQuestion', ['test' => $test->id, 'question' => $question->id]) }}"
            method="POST"
            style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
    <script src="
        https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.umd.min.js
        "></script>

    <script>
        // Переключение темы
        const toggleBtn = document.getElementById("theme-toggle");
        toggleBtn.addEventListener("click", () => {
            document.body.classList.toggle("dark-theme");
            toggleBtn.textContent = document.body.classList.contains("dark-theme") ?
                "☀️ Светлая тема" :
                "🌙 Тёмная тема";
        });

        // Работа с вариантами
        const questionTypeSelect = document.getElementById('question_type');
        const optionsContainer = document.getElementById('options-container');
        const optionsGroup = document.getElementById('options-group');
        const textAnswersGroup = document.getElementById('text-answers-group');
        const textAnswersContainer = document.getElementById('text-answers-container');
        const addBtn = document.getElementById("add-option");
        const addTextAnswerBtn = document.getElementById("add-text-answer");

        function updateQuestionTypeUI() {
            const type = questionTypeSelect.value;
            const optionInputs = optionsContainer.querySelectorAll('input[type="text"], input[type="radio"], input[type="checkbox"]');
            const textAnswerInputs = textAnswersContainer.querySelectorAll('input[type="text"]');
            
            if (type === 'short_answer') {
                optionsGroup.style.display = 'none';
                textAnswersGroup.style.display = 'block';
                
                // Убираем required у скрытых полей опций
                optionInputs.forEach(input => {
                    input.required = false;
                });
                
                // Добавляем required к полям текстовых ответов
                textAnswerInputs.forEach(input => {
                    input.required = true;
                });
            } else if (type === 'rich_text_answer') {
                // Для развёрнутых ответов не требуется задавать правильные варианты
                optionsGroup.style.display = 'none';
                textAnswersGroup.style.display = 'none';

                // Убираем required у всех полей вариантов и текстовых ответов
                optionInputs.forEach(input => {
                    input.required = false;
                });
                textAnswerInputs.forEach(input => {
                    input.required = false;
                });
            } else {
                optionsGroup.style.display = 'block';
                textAnswersGroup.style.display = 'none';
                
                // Добавляем required к полям опций
                optionInputs.forEach(input => {
                    if (input.type === 'text') {
                        input.required = true;
                    }
                });
                
                // Убираем required у скрытых полей текстовых ответов
                textAnswerInputs.forEach(input => {
                    input.required = false;
                });
                
                updateOptionInputs();
            }
        }

        function updateOptionInputs() {
            const type = questionTypeSelect.value;
            Array.from(optionsContainer.children).forEach((optionDiv, index) => {
                const input = optionDiv.querySelector('input[type="radio"], input[type="checkbox"]');
                if (type === 'single_choice') {
                    input.type = 'radio';
                    input.name = 'correct_option';
                    input.required = true;
                } else {
                    input.type = 'checkbox';
                    input.name = 'correct_options[]';
                    input.required = false;
                }
            });
        }

        questionTypeSelect.addEventListener('change', updateQuestionTypeUI);
        updateQuestionTypeUI();

        addBtn.addEventListener("click", function() {
            const index = optionsContainer.children.length;
            const type = questionTypeSelect.value;
            const inputType = type === 'single_choice' ? 'radio' : 'checkbox';
            const nameAttr = type === 'single_choice' ? 'correct_option' : 'correct_options[]';

            const newOption = document.createElement("div");
            newOption.className = "option-item";
            newOption.innerHTML = `
            <input type="${inputType}" name="${nameAttr}" value="${index}">
            <input type="text" name="options[${index}]" placeholder="Вариант ${index + 1}" required>
        `;
            optionsContainer.appendChild(newOption);
            
            // Убедимся, что новые поля имеют правильный required статус
            if (optionsGroup.style.display === 'none') {
                const inputs = newOption.querySelectorAll('input');
                inputs.forEach(input => {
                    input.required = false;
                });
            }
        });

        addTextAnswerBtn.addEventListener("click", function() {
            const index = textAnswersContainer.children.length;
            const newAnswer = document.createElement("div");
            newAnswer.className = "text-answer-item";
            newAnswer.innerHTML = `
            <input type="text" name="correct_answers[${index}]" placeholder="Правильный ответ ${index + 1}" required>
        `;
            textAnswersContainer.appendChild(newAnswer);
            
            // Убедимся, что новые поля имеют правильный required статус
            if (textAnswersGroup.style.display === 'none') {
                const input = newAnswer.querySelector('input');
                input.required = false;
            }
        });

        document.addEventListener("trix-attachment-add", function(event) {
            const attachment = event.attachment;

            if (attachment.file) {
                uploadAttachment(attachment);
            }
        });

        function uploadAttachment(attachment) {
            const file = attachment.file;
            const formData = new FormData();
            formData.append("file", file);

            fetch("{{ route('questions.upload') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData,
                    credentials: "same-origin"
                })
                .then(response => response.json())
                .then(data => {
                    if (data.location) {
                        // Устанавливаем URL загруженного изображения в Trix
                        attachment.setAttributes({
                            url: data.location,
                            href: data.location
                        });
                    }
                })
                .catch(() => alert("Ошибка загрузки изображения"));
        }

        // Обработка отправки формы
        const questionForm = document.getElementById('question-form');
        questionForm.addEventListener('submit', function(e) {
            const type = questionTypeSelect.value;
            
            // Удаляем атрибут name у скрытых полей (браузер будет их игнорировать)
            if (type === 'short_answer') {
                // Удаляем name у полей опций
                optionsContainer.querySelectorAll('input').forEach(input => {
                    input.removeAttribute('name');
                });
            } else if (type === 'rich_text_answer') {
                // Удаляем name у полей опций
                optionsContainer.querySelectorAll('input').forEach(input => {
                    input.removeAttribute('name');
                });
                // Удаляем name у полей простого текста
                textAnswersContainer.querySelectorAll('input[type="text"]').forEach(input => {
                    input.removeAttribute('name');
                });
            } else {
                // Удаляем name у полей текстовых ответов
                textAnswersContainer.querySelectorAll('input[type="text"]').forEach(input => {
                    input.removeAttribute('name');
                });
            }
            
            const submitBtn = questionForm.querySelector('.submit-btn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Создание вопроса...';
            
            // Небольшая задержка перед перезагрузкой, чтобы данные успели сохраниться
            setTimeout(() => {
                location.reload();
            }, 1500);
        });
    </script>
</body>

</html>
