<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование вопроса</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen p-6">

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Редактирование вопроса</h1>
        <p class="text-gray-600 mt-2">ID вопроса: {{ $question->id }}</p>
    </div>

    @if($errors->any())
        <div class="p-4 mb-6 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.question-bank.update', $question) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Question Text -->
        <div class="bg-white p-6 rounded-lg shadow">
            <label for="question_text" class="block text-lg font-semibold text-gray-800 mb-3">Текст вопроса</label>
            <textarea id="question_text" name="question_text" required rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Введите текст вопроса">{{ old('question_text', $question->question_text) }}</textarea>
        </div>

        <!-- Question Type -->
        <div class="bg-white p-6 rounded-lg shadow">
            <label for="question_type" class="block text-lg font-semibold text-gray-800 mb-3">Тип вопроса</label>
            <select id="question_type" name="question_type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="multiple_choice" {{ $question->question_type === 'multiple_choice' ? 'selected' : '' }}>Множественный выбор</option>
                <option value="true_false" {{ $question->question_type === 'true_false' ? 'selected' : '' }}>Верно/Неверно</option>
                <option value="short_answer" {{ $question->question_type === 'short_answer' ? 'selected' : '' }}>Короткий ответ</option>
            </select>
        </div>

        <!-- Options -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between mb-4">
                <label class="text-lg font-semibold text-gray-800">Варианты ответов</label>
                <button type="button" id="addOptionBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                    + Добавить вариант
                </button>
            </div>

            <div id="optionsContainer" class="space-y-4">
                @forelse($question->options as $index => $option)
                    <div class="option-row p-4 border border-gray-300 rounded-lg bg-gray-50">
                        <div class="flex items-start gap-4">
                            <div class="flex-1 space-y-3">
                                <input type="hidden" name="options[{{ $index }}][id]" value="{{ $option->id }}">
                                <textarea name="options[{{ $index }}][option_text]" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Введите текст варианта ответа">{{ $option->option_text }}</textarea>
                                
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="options[{{ $index }}][is_correct]" value="1" {{ $option->is_correct ? 'checked' : '' }} class="w-4 h-4 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                                    <span class="text-sm font-medium text-gray-700">Это правильный ответ</span>
                                </label>
                            </div>
                            <button type="button" class="removeOptionBtn px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition mt-1">
                                ✕
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-gray-500">
                        Еще нет вариантов ответов. Добавьте первый вариант.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3">
            <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-lg">
                ✓ Сохранить изменения
            </button>
            <a href="{{ route('admin.question-bank.index') }}" class="flex-1 px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-semibold text-lg text-center">
                Отмена
            </a>
        </div>
    </form>
</div>

<script>
    let optionIndex = {{ count($question->options) }};

    document.getElementById('addOptionBtn').addEventListener('click', function() {
        const container = document.getElementById('optionsContainer');
        
        const optionRow = document.createElement('div');
        optionRow.className = 'option-row p-4 border border-gray-300 rounded-lg bg-gray-50';
        optionRow.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="flex-1 space-y-3">
                    <textarea name="options[${optionIndex}][option_text]" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Введите текст варианта ответа"></textarea>
                    
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="options[${optionIndex}][is_correct]" value="1" class="w-4 h-4 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                        <span class="text-sm font-medium text-gray-700">Это правильный ответ</span>
                    </label>
                </div>
                <button type="button" class="removeOptionBtn px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition mt-1">
                    ✕
                </button>
            </div>
        `;
        
        container.appendChild(optionRow);
        optionIndex++;
        
        addRemoveListener(optionRow.querySelector('.removeOptionBtn'));
    });

    function addRemoveListener(btn) {
        btn.addEventListener('click', function() {
            if (document.querySelectorAll('.option-row').length > 1) {
                this.closest('.option-row').remove();
            } else {
                alert('Должен быть хотя бы один вариант ответа!');
            }
        });
    }

    document.querySelectorAll('.removeOptionBtn').forEach(btn => addRemoveListener(btn));
</script>

</body>
</html>
