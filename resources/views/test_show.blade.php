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
        <!-- Список вопросов теста -->
        <div>
            <div class="card-title">
                <h2>{{ $test->title }}</h2>
                <p>{{ $test->description }}</p>
            </div>
            <div class="card">
                <h3 class="text-xl font-bold mb-4">Вопросы в тесте ({{ $test->questions->count() }})</h3>
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
                        </select>
                    </div>

                    <!-- Варианты ответов -->
                    <div class="form-group">
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
        const addBtn = document.getElementById("add-option");

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

        questionTypeSelect.addEventListener('change', updateOptionInputs);
        updateOptionInputs();

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
    </script>
</body>

</html>
