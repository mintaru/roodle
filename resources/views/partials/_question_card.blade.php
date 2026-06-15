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
            $selectHTML = '<select class="fill-in-dropdown-select-inline" data-question-id="' . $question->id . '" data-blank-id="' . $blankId . '">';
            $selectHTML .= '<option value="">—выберите—</option>';
            foreach ($options as $option) {
                $selectedAttr = $savedValue == $option['id'] ? 'selected' : '';
                $selectHTML .= '<option value="' . $option['id'] . '" ' . $selectedAttr . '>' . htmlspecialchars($option['text'], ENT_QUOTES, 'UTF-8') . '</option>';
            }
            $selectHTML .= '</select>';
            $questionDisplay = str_replace('{' . $blankId . '}', $selectHTML, $questionDisplay);
        }
    }
@endphp

<div class="question-card" data-question-id="{{ $question->id }}">
    <div class="question-card__header">
        <div class="question-badge">{{ $questionIndex + 1 }}</div>
        <p class="question-text">{!! $questionDisplay !!}</p>
        <button type="button" class="clearCurrentBtn" data-question-id="{{ $question->id }}"
            title="Очистить ответ на этот вопрос">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
    </div>

    <div class="question-card__body">
        @if ($question->question_type === 'short_answer')
            @php
                $savedText = '';
                if (isset($savedAnswers[$question->id]) && is_string($savedAnswers[$question->id])) {
                    $savedText = $savedAnswers[$question->id];
                }
            @endphp
            <div class="short-answer-wrap">
                <label>Ваш ответ</label>
                <input type="text"
                    name="text_answers[{{ $question->id }}]"
                    class="short-answer-input text-answer-input"
                    placeholder="Введите ответ..."
                    value="{{ $savedText }}"
                    data-question-id="{{ $question->id }}">
            </div>

        @elseif ($question->question_type === 'rich_text_answer')
            @php
                $savedRichText = '';
                if (isset($savedAnswers[$question->id]) && is_string($savedAnswers[$question->id])) {
                    $savedRichText = $savedAnswers[$question->id];
                }
            @endphp
            <div class="rich-text-wrap">
                <label>Развёрнутый ответ</label>
                <input type="hidden"
                    id="rich_text_answer_{{ $question->id }}"
                    name="rich_text_answers[{{ $question->id }}]"
                    value="{{ $savedRichText }}">
                <trix-editor
                    input="rich_text_answer_{{ $question->id }}"
                    data-question-id="{{ $question->id }}"
                    class="rich-text-answer-input"></trix-editor>
            </div>

        @elseif ($question->question_type === 'fill_in_dropdown')
            {{-- dropdowns are already rendered inline in question text above --}}

        @else
            <div class="option-list">
                @foreach ($question->options as $option)
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
                    <label class="option-label">
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
            </div>
        @endif
    </div>
</div>
