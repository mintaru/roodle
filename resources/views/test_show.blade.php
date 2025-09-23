<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $test->title }}</title>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
<header class="header">
    <img src="{{ asset('Images/Logo.png') }}" alt="Логотип" class="logo">
    <button id="theme-toggle" class="theme-toggle-btn">🌙 Тёмная тема</button>
</header>

<div class="container-main">
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
                        <p class="font-semibold">{{ $loop->iteration }}. {{ $question->question_text }}</p>
                        <ul class="options-list mt-2">
                            @foreach($question->options as $option)
                                <li class="{{ $option->is_correct ? 'correct-answer' : '' }}">
                                    {{ $option->option_text }}
                                    @if($option->is_correct)
                                        <span class="correct-label">(Верный ответ)</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <p class="no-questions">В этом тесте еще нет вопросов.</p>
                @endforelse
            </div>
        </div>
    </div>
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
                <div class="form-group">
                    <label for="question_text">Текст вопроса</label>
                    <textarea name="question_text" id="question_text" rows="3" required></textarea>
                </div>
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
                <button type="submit" class="submit-btn">Добавить вопрос</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById("add-option").addEventListener("click", function() {
        const container = document.getElementById("options-container");
        const index = container.children.length;
        const newOption = document.createElement("div");
        newOption.className = "option-item";
        newOption.innerHTML = `
            <input type="radio" name="correct_option" value="${index}">
            <input type="text" name="options[${index}]" placeholder="Вариант ${index + 1}" required>
        `;
        container.appendChild(newOption);
    });

    const toggleBtn = document.getElementById("theme-toggle");
    toggleBtn.addEventListener("click", () => {
        document.body.classList.toggle("dark-theme");
        if (document.body.classList.contains("dark-theme")) {
            toggleBtn.textContent = "☀️ Светлая тема";
        } else {
            toggleBtn.textContent = "🌙 Тёмная тема";
        }
    });
</script>

</body>
</html>
