@extends('layout')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="server-time" content="{{ now()->timestamp * 1000 }}">
    <meta name="test-start-time" content="{{ $attempt->started_at ? $attempt->started_at->timestamp * 1000 : now()->timestamp * 1000 }}">
    <link href="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.umd.min.js"></script>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="card">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold">{{ $test->title }}</h2>
                    <p class="text-center text-gray-600 mt-2">{{ $test->description }}</p>
                </div>
                @if ($test->time_limit > 0)
                    <div class="text-center p-4 bg-yellow-100 rounded-lg border-2 border-yellow-400">
                        <div class="text-sm text-gray-700 mb-2">Время на тест:</div>
                        <div id="timer" class="text-3xl font-bold text-red-600">{{ floor($test->time_limit) }} :00</div>
                    </div>
                @endif
            </div>

            <form action="/tests/{{ $test->id }}/result" method="POST" id="testForm">
                @csrf
                <div class="space-y-8">
                    @foreach ($test->questions as $question)
                        <div class="border-t pt-6">
                            <p class="font-semibold text-lg mb-4">{{ $loop->iteration }}. {!! $question->question_text !!}</p>
                            <div class="space-y-3 pl-4">
                                @if ($question->question_type === 'short_answer')
                                    {{-- Текстовый ответ --}}
                                    @php
                                        $savedText = '';
                                        if (isset($savedAnswers[$question->id])) {
                                            $savedText = $savedAnswers[$question->id];
                                        }
                                    @endphp
                                    <input
                                        type="text"
                                        name="text_answers[{{ $question->id }}]"
                                        class="text-answer-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Введите ответ..."
                                        value="{{ $savedText }}"
                                        data-question-id="{{ $question->id }}">
                                @elseif ($question->question_type === 'rich_text_answer')
                                    {{-- Развёрнутый ответ --}}
                                    @php
                                        $savedRichText = '';
                                        if (isset($savedAnswers[$question->id]) && is_string($savedAnswers[$question->id])) {
                                            $savedRichText = $savedAnswers[$question->id];
                                        }
                                    @endphp
                                    <input type="hidden" id="rich_text_answer_{{ $question->id }}" name="rich_text_answers[{{ $question->id }}]" value="{{ $savedRichText }}">
                                    <trix-editor input="rich_text_answer_{{ $question->id }}" data-question-id="{{ $question->id }}" class="rich-text-answer-input"></trix-editor>
                                @else
                                    {{-- Варианты выбора --}}
                                    @foreach ($question->options as $option)
                                        @php
                                            $isChecked = false;
                                            if (isset($savedAnswers[$question->id]) && is_array($savedAnswers[$question->id])) {
                                                if ($question->question_type === 'single_choice') {
                                                    // Для single_choice сравниваем с первым элементом массива
                                                    $isChecked = $savedAnswers[$question->id][0] == $option->id;
                                                } else {
                                                    // Для multiple_choice проверяем наличие значения в массиве
                                                    $isChecked = in_array($option->id, $savedAnswers[$question->id]);
                                                }
                                            }
                                        @endphp
                                        <label
                                            class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors">
                                            <input
                                                type="{{ $question->question_type === 'single_choice' ? 'radio' : 'checkbox' }}"
                                                name="answers[{{ $question->id }}]{{ $question->question_type === 'multiple_choice' ? '[]' : '' }}"
                                                value="{{ $option->id }}" class="answer-input"
                                                data-question-id="{{ $question->id }}" {{ $isChecked ? 'checked' : '' }}>
                                            <span>{{ $option->option_text }}</span>
                                        </label>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-8 text-center">
                    <button type="submit" class="btn btn-primary text-lg">Завершить тест и узнать результат</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.answer-input');
            const textInputs = document.querySelectorAll('.text-answer-input');
            const richTextEditors = document.querySelectorAll('.rich-text-answer-input');

            inputs.forEach(input => {
                input.addEventListener('change', async function() {
                    try {
                        const questionId = this.dataset.questionId;
                        let optionIds;

                        if (this.type === 'radio') {
                            optionIds = this.value;
                        } else { // checkbox
                            const checkedBoxes = document.querySelectorAll(
                                `input[name="answers[${questionId}][]"]:checked`);
                            optionIds = Array.from(checkedBoxes).map(cb => cb.value);
                        }

                        const response = await fetch(
                            `/tests/{{ $test->id }}/save-answer`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document
                                        .querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify({
                                    question_id: questionId,
                                    option_id: optionIds
                                })
                            });

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                    } catch (error) {
                        console.error('Error saving answer:', error);
                    }
                });
            });

            // Сохранение текстовых ответов
            textInputs.forEach(input => {
                input.addEventListener('change', async function() {
                    try {
                        const questionId = this.dataset.questionId;
                        const answerText = this.value;

                        const response = await fetch(
                            `/tests/{{ $test->id }}/save-answer`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document
                                        .querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify({
                                    question_id: questionId,
                                    answer_text: answerText
                                })
                            });

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                    } catch (error) {
                        console.error('Error saving text answer:', error);
                    }
                });
            });

            // Сохранение развёрнутых ответов (Trix editors)
            richTextEditors.forEach(editor => {
                editor.addEventListener('trix-change', async function() {
                    try {
                        const questionId = this.dataset.questionId;
                        const inputId = this.getAttribute('input');
                        const answerText = document.getElementById(inputId).value;

                        const response = await fetch(
                            `/tests/{{ $test->id }}/save-answer`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document
                                        .querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify({
                                    question_id: questionId,
                                    rich_text_answer: answerText
                                })
                            });

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                    } catch (error) {
                        console.error('Error saving rich text answer:', error);
                    }
                });
            });

            // Таймер - синхронизация с серверным временем при загрузке
            @if ($test->time_limit > 0)
                const serverTimeMeta = parseInt(document.querySelector('meta[name="server-time"]')?.content || 0);
                const testStartTimeMeta = parseInt(document.querySelector('meta[name="test-start-time"]')
                    ?.content || 0);
                const clientTimeAtLoad = Date.now();
                const timerElement = document.getElementById('timer');
                const testForm = document.getElementById('testForm');
                const timeLimitMs = {{ $test->time_limit }} * 60 * 1000;
                let timerInterval;

                function updateTimer() {
                    // Рассчитываем текущее серверное время на основе смещения
                    const timeSinceLoad = Date.now() - clientTimeAtLoad;
                    const currentServerTime = serverTimeMeta + timeSinceLoad;

                    // Оставшееся время
                    const elapsedMs = currentServerTime - testStartTimeMeta;
                    const timeLeftMs = Math.max(0, timeLimitMs - elapsedMs);
                    const timeLeftSeconds = Math.round(timeLeftMs / 1000);

                    const minutes = Math.floor(timeLeftSeconds / 60);
                    const seconds = timeLeftSeconds % 60;

                    timerElement.textContent = `${minutes}:${String(seconds).padStart(2, '0')}`;

                    if (timeLeftSeconds <= 60) {
                        timerElement.classList.add('animate-pulse');
                    }

                    if (timeLeftSeconds <= 0) {
                        timerElement.textContent = '00:00';
                        clearInterval(timerInterval);
                        //чтобы уведомление всплывало о том что автоматически ответы скинулись
                        testForm.submit();
                        return;
                    }
                }

                updateTimer(); // сразу показать стартовое время
                timerInterval = setInterval(updateTimer, 1000);
            @endif

        });
    </script>
@endsection
