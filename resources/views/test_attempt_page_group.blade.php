@extends('layout')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.umd.min.js"></script>
    <style>
        trix-editor.rich-text-answer-input {
            min-height: 160px;
            width: 100%;
            overflow: visible;
        }

        trix-editor ul,
        trix-editor ol,
        .trix-content ul,
        .trix-content ol {
            list-style-type: disc;
            list-style-position: outside;
            margin-left: 1.5rem;
        }
    </style>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card">
        <h2 class="text-2xl font-bold text-center">{{ $test->title }}</h2>
        <p class="text-center text-gray-600 mt-2 mb-8">
            Страница {{ $pageIndex }} из {{ $totalPages }}
        </p>

        <form action="/tests/{{ $test->id }}/result" method="POST">
            @csrf

            @foreach($questions as $indexOnPage => $question)
                <div class="border-t pt-6">
                    @php
                        $questionDisplay = $question->question_text;
                        if ($question->question_type === 'fill_in_dropdown') {
                            $dropdownsByBlank = [];
                            foreach ($question->options as $option) {
                                $data = json_decode($option->option_text, true);
                                if (!isset($dropdownsByBlank[$data['blank_id']])) {
                                    $dropdownsByBlank[$data['blank_id']] = [];
                                }
                                $dropdownsByBlank[$data['blank_id']][] = [
                                    'id' => $option->id,
                                    'text' => $data['text'],
                                ];
                            }
                            
                            foreach ($dropdownsByBlank as $blankId => $options) {
                                $savedValue = '';
                                if (isset($savedAnswers[$question->id]) && is_array($savedAnswers[$question->id])) {
                                    $savedValue = $savedAnswers[$question->id][$blankId] ?? '';
                                }
                                
                                $selectHTML = '<select class="fill-in-dropdown-select-inline" data-question-id="' . $question->id . '" data-blank-id="' . $blankId . '" style="display: inline-block; padding: 4px 8px; margin: 0 4px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;">';
                                $selectHTML .= '<option value="">--</option>';
                                foreach ($options as $option) {
                                    $selectedAttr = ($savedValue == $option['id']) ? 'selected' : '';
                                    $selectHTML .= '<option value="' . $option['id'] . '" ' . $selectedAttr . '>' . htmlspecialchars($option['text'], ENT_QUOTES, 'UTF-8') . '</option>';
                                }
                                $selectHTML .= '</select>';
                                
                                $questionDisplay = str_replace('{' . $blankId . '}', $selectHTML, $questionDisplay);
                            }
                        }
                    @endphp
                    <p class="font-semibold text-lg mb-4">
                        {{ $globalIndexMap[$question->id] ?? ($indexOnPage + 1) }}. {!! $questionDisplay !!}
                    </p>
                    <div class="space-y-3 pl-4">
                        @if ($question->question_type === 'short_answer')
                            @php
                                $savedText = '';
                                if (isset($savedAnswers[$question->id]) && is_string($savedAnswers[$question->id])) {
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
                            @php
                                $savedRichText = '';
                                if (isset($savedAnswers[$question->id]) && is_string($savedAnswers[$question->id])) {
                                    $savedRichText = $savedAnswers[$question->id];
                                }
                            @endphp
                            <input type="hidden" id="rich_text_answer_{{ $question->id }}" name="rich_text_answers[{{ $question->id }}]" value="{{ $savedRichText }}">
                            <trix-editor input="rich_text_answer_{{ $question->id }}" data-question-id="{{ $question->id }}" class="rich-text-answer-input"></trix-editor>
                        @elseif ($question->question_type !== 'fill_in_dropdown')
                            @foreach($question->options as $option)
                                @php
                                    $isChecked = false;
                                    if (isset($savedAnswers[$question->id]) && is_array($savedAnswers[$question->id])) {
                                        if ($question->question_type === 'single_choice') {
                                            $isChecked = $savedAnswers[$question->id][0] == $option->id;
                                        } else {
                                            $isChecked = in_array($option->id, $savedAnswers[$question->id]);
                                        }
                                    }
                                @endphp
                                <label class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors">
                                    <input
                                        type="{{ $question->question_type === 'single_choice' ? 'radio' : 'checkbox' }}"
                                        name="answers[{{ $question->id }}]{{ $question->question_type === 'multiple_choice' ? '[]' : '' }}"
                                        value="{{ $option->id }}"
                                        class="answer-input"
                                        data-question-id="{{ $question->id }}"
                                        {{ $isChecked ? 'checked' : '' }}>
                                    <span>{{ $option->option_text }}</span>
                                </label>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="mt-8 flex justify-between items-center">
                @if($pageIndex > 1)
                    <a href="{{ route('tests.attempt.page', [$test->id, $pageIndex - 1]) }}" class="btn btn-secondary">
                        ← Предыдущая страница
                    </a>
                @else
                    <span></span>
                @endif

                @if($pageIndex < $totalPages)
                    <a href="{{ route('tests.attempt.page', [$test->id, $pageIndex + 1]) }}" class="btn btn-primary">
                        Следующая страница →
                    </a>
                @else
                    <button type="submit" class="btn btn-primary"
                            onclick="return confirm('Завершить тест и отправить ответы?');">
                        Завершить тест
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.answer-input');
        const textInputs = document.querySelectorAll('.text-answer-input');
        const richTextEditors = document.querySelectorAll('.rich-text-answer-input');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        document.addEventListener('trix-file-accept', function (event) {
            event.preventDefault();
        });

        document.addEventListener('trix-attachment-add', function (event) {
            if (event.attachment) {
                event.attachment.remove();
            }
        });

        inputs.forEach(input => {
            input.addEventListener('change', async function() {
                try {
                    const questionId = this.dataset.questionId;
                    let optionIds;

                    if (this.type === 'radio') {
                        optionIds = [this.value];
                    } else {
                        const checkedBoxes = document.querySelectorAll(`input[name="answers[${questionId}][]"]:checked`);
                        optionIds = Array.from(checkedBoxes).map(cb => cb.value);
                    }

                    const response = await fetch(`/tests/{{ $test->id }}/save-answer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            question_id: questionId,
                            option_id: optionIds
                        })
                    });

                    if (!response.ok) {
                        console.error('Save-answer failed', response.status);
                    }
                } catch (error) {
                    console.error('Error saving answer:', error);
                }
            });
        });

        textInputs.forEach(input => {
            input.addEventListener('change', async function() {
                try {
                    const questionId = this.dataset.questionId;
                    const answerText = this.value;

                    const response = await fetch(`/tests/{{ $test->id }}/save-answer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            question_id: questionId,
                            answer_text: answerText
                        })
                    });

                    if (!response.ok) {
                        console.error('Save text answer failed', response.status);
                    }
                } catch (error) {
                    console.error('Error saving text answer:', error);
                }
            });
        });

        richTextEditors.forEach(editor => {
            editor.addEventListener('trix-change', async function() {
                try {
                    const questionId = this.dataset.questionId;
                    const inputId = this.getAttribute('input');
                    const answerText = document.getElementById(inputId).value;

                    const response = await fetch(`/tests/{{ $test->id }}/save-answer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            question_id: questionId,
                            rich_text_answer: answerText
                        })
                    });

                    if (!response.ok) {
                        console.error('Save rich text answer failed', response.status);
                    }
                } catch (error) {
                    console.error('Error saving rich text answer:', error);
                }
            });
        });

        // Сохранение ответов для fill_in_dropdown
        const dropdownSelects = document.querySelectorAll('.fill-in-dropdown-select-inline');
        dropdownSelects.forEach(select => {
            select.addEventListener('change', async function() {
                try {
                    const questionId = this.dataset.questionId;
                    
                    // Собираем все выбранные ответы для всех пропусков в этом вопросе
                    const filledAnswers = {};
                    document.querySelectorAll(`.fill-in-dropdown-select-inline[data-question-id="${questionId}"]`).forEach(sel => {
                        const blankId = sel.dataset.blankId;
                        const selectedValue = sel.value;
                        if (selectedValue) {
                            filledAnswers[blankId] = parseInt(selectedValue);
                        }
                    });

                    const response = await fetch(`/tests/{{ $test->id }}/save-answer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            question_id: questionId,
                            fill_in_dropdown_answers: filledAnswers
                        })
                    });

                    if (!response.ok) {
                        console.error('Save fill_in_dropdown answer failed', response.status);
                    }
                } catch (error) {
                    console.error('Error saving fill_in_dropdown answer:', error);
                }
            });
        });
    });
</script>
@endsection

