<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование вопроса</title>
    <link href="{{ asset('css/tailwind.min.css') }}" rel="stylesheet">
    <style>
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-backdrop.active { display: flex; }
        .modal-box {
            background: #fff;
            border-radius: 28px;
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 24px 60px rgba(0, 0, 0, .2);
            animation: modalIn .2s ease;
            text-align: center;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: scale(.95); }
            to { opacity: 1; transform: none; }
        }
        .modal-icon {
            width: 52px;
            height: 52px;
            border-radius: 20px;
            background: #fff3e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }
        .modal-box h3 {
            font-size: 18px;
            font-weight: 700;
            color: #1e2530;
            margin-bottom: .5rem;
        }
        .modal-box p {
            font-size: 14px;
            color: #6b7a89;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .modal-btn {
            padding: 11px 32px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            border: none;
            cursor: pointer;
            transition: .2s ease;
            background: #00b5a5;
            color: #fff;
            box-shadow: 0 4px 14px rgba(0, 181, 165, .3);
        }
        .modal-btn:hover {
            background: #009e90;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen p-6">

<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <x-back-button :url="route('admin.question-bank.index')" text="К банку вопросов" />
    </div>
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
            <textarea id="question_text" name="question_text" required rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Введите текст вопроса">{{ old('question_text', $question->question_text)}}</textarea>
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
                 Сохранить изменения
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
                showModal('Должен быть хотя бы один вариант ответа!');
            }
        });
    }

    document.querySelectorAll('.removeOptionBtn').forEach(btn => addRemoveListener(btn));

    function showModal(msg) {
        document.getElementById('modalMessage').textContent = msg;
        document.getElementById('alertModal').classList.add('active');
    }
</script>

<div class="modal-backdrop" id="alertModal">
    <div class="modal-box">
        <div class="modal-icon">
            <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="#e65100" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            </svg>
        </div>
        <h3>Внимание</h3>
        <p id="modalMessage"></p>
        <button class="modal-btn" onclick="document.getElementById('alertModal').classList.remove('active')">OK</button>
    </div>
</div>

<script>
    document.getElementById('alertModal').addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('active');
    });
</script>

</body>
</html>
