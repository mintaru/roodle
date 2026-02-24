<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Детали попытки: {{ $user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .header-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .info-box {
            background: #f9f9f9;
            padding: 12px;
            border-radius: 6px;
            border-left: 4px solid #2c3e50;
        }

        .info-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-top: 4px;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 16px;
            background: #ccc;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: background 0.3s;
        }

        .back-button:hover {
            background: #aaa;
        }

        .question-card {
            background: white;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .question-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .question-number {
            font-size: 13px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
        }

        .question-text {
            font-size: 16px;
            font-weight: 500;
            color: #333;
            margin: 8px 0 0 0;
        }

        .question-type {
            display: inline-block;
            font-size: 11px;
            padding: 4px 8px;
            margin-top: 8px;
            background: #e8f4f8;
            color: #0891b2;
            border-radius: 4px;
            font-weight: 600;
        }

        .correctness-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .correctness-badge.correct {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .correctness-badge.incorrect {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .correctness-badge.empty {
            background: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }

        .question-body {
            padding: 20px;
        }

        .option {
            padding: 12px 15px;
            margin-bottom: 10px;
            border: 2px solid #eee;
            border-radius: 6px;
            background: #f9f9f9;
            transition: all 0.2s;
        }

        .option:last-child {
            margin-bottom: 0;
        }

        .option.selected {
            background: #fff9e6;
            border-color: #ffc107;
        }

        .option.correct {
            background: #e8f5e9;
            border-color: #4caf50;
            border-width: 2px;
        }

        .option.selected.correct {
            background: #c8e6c9;
            border-color: #2e7d32;
        }

        .option.selected.incorrect {
            background: #ffebee;
            border-color: #d32f2f;
        }

        .option-label {
            display: flex;
            align-items: flex-start;
        }

        .option-radio {
            margin-right: 12px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .option-text {
            flex: 1;
        }

        .option-badge {
            display: inline-block;
            margin-left: 8px;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 3px;
        }

        .option-badge.user-answer {
            background: #fff3cd;
            color: #856404;
        }

        .option-badge.correct-answer {
            background: #d4edda;
            color: #155724;
        }

        .text-answer-box {
            background: #f9f9f9;
            padding: 15px;
            border: 2px solid #eee;
            border-radius: 6px;
            margin-top: 10px;
            font-family: 'Courier New', monospace;
            color: #333;
        }

        .text-answer-box.correct {
            background: #e8f5e9;
            border-color: #4caf50;
        }

        .text-answer-box.incorrect {
            background: #ffebee;
            border-color: #d32f2f;
        }

        .no-answer {
            padding: 15px;
            background: #f0f0f0;
            border-radius: 6px;
            color: #999;
            font-style: italic;
            margin-top: 10px;
        }

        .correct-answers {
            margin-top: 15px;
            padding: 12px;
            background: #f0f7f4;
            border-left: 4px solid #27ae60;
            border-radius: 4px;
        }

        .correct-answers-title {
            font-weight: 600;
            color: #27ae60;
            font-size: 13px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        @media (max-width: 768px) {
            .question-header {
                flex-direction: column;
            }

            .correctness-badge {
                margin-top: 10px;
            }

            .header-info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
@include('components.menu')

<div class="container">
    <a href="{{ route('tests.results', $test) }}" class="back-button">← Назад к результатам</a>

    <div class="header">
        <h1>Детали попытки: {{ $test->title }}</h1>
        
        <div class="header-info">
            <div class="info-box">
                <div class="info-label">Студент</div>
                <div class="info-value">{{ $user->name }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Попытка</div>
                <div class="info-value">#{{ $attempt->attempt_number }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Результат</div>
                <div class="info-value" style="color: {{ $attempt->score >= 70 ? '#27ae60' : ($attempt->score >= 50 ? '#f39c12' : '#e74c3c') }};">
                    {{ $attempt->score }}%
                </div>
            </div>
            <div class="info-box">
                <div class="info-label">Дата завершения</div>
                <div class="info-value">{{ $attempt->ended_at->format('d.m.Y H:i') }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Время затрачено</div>
                <div class="info-value">{{ \App\Helpers\TimeFormatter::formatMinutes($attempt->started_at->diffInMinutes($attempt->ended_at)) }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Курс</div>
                <div class="info-value">{{ $course->title }}</div>
            </div>
        </div>
    </div>

    <h2 style="margin-bottom: 20px; color: #2c3e50;">Ответы студента</h2>

    @foreach($questionDetails as $index => $detail)
        @php
            $question = $detail['question'];
            $isCorrect = $detail['is_correct'];
            $badgeClass = $isCorrect ? 'correct' : ($detail['user_answer_text'] || count($detail['user_selected_option_ids']) > 0 ? 'incorrect' : 'empty');
            $badgeText = $isCorrect ? '✓ Правильно' : (($detail['user_answer_text'] || count($detail['user_selected_option_ids']) > 0) ? '✗ Неправильно' : 'Не ответил');
        @endphp

        <div class="question-card">
            <div class="question-header">
                <div>
                    <div class="question-number">Вопрос {{ $index + 1 }}</div>
                    <div class="question-text">{{ strip_tags($question->question_text) }}</div>
                    <span class="question-type">{{ 
                        $question->question_type === 'single_choice' ? 'Один ответ' : 
                        ($question->question_type === 'multiple_choice' ? 'Несколько ответов' : 
                        ($question->question_type === 'rich_text_answer' ? 'Развёрнутый ответ' : 'Текстовый ответ'))
                    }}</span>
                </div>
                <span class="correctness-badge {{ $badgeClass }}">{{ $badgeText }}</span>
            </div>

            <div class="question-body">
                @if($question->question_type === 'short_answer')
                    <div style="margin-bottom: 15px;">
                        <strong style="display: block; margin-bottom: 8px; color: #666; font-size: 13px; text-transform: uppercase;">Ответ студента:</strong>
                        @if($detail['user_answer_text'])
                            <div class="text-answer-box {{ $isCorrect ? 'correct' : 'incorrect' }}">
                                {{ $detail['user_answer_text'] }}
                            </div>
                        @else
                            <div class="no-answer">Студент не дал ответ</div>
                        @endif
                    </div>

                    @if(!$isCorrect)
                        <div class="correct-answers">
                            <div class="correct-answers-title">Правильные ответы:</div>
                            @foreach($question->options->where('is_correct', true) as $option)
                                <div>• {{ $option->option_text }}</div>
                            @endforeach
                        </div>
                    @endif

                @elseif($question->question_type === 'rich_text_answer')
                    <div style="margin-bottom: 15px;">
                        <strong style="display: block; margin-bottom: 8px; color: #666; font-size: 13px; text-transform: uppercase;">Ответ студента:</strong>
                        @if($detail['user_answer_text'])
                            <div class="text-answer-box {{ $isCorrect ? 'correct' : 'incorrect' }}">
                                {!! $detail['user_answer_text'] !!}
                            </div>
                        @else
                            <div class="no-answer">Студент не дал ответ</div>
                        @endif
                    </div>

                    @if(!$isCorrect)
                        <div class="correct-answers">
                            <div class="correct-answers-title">Правильные ответы:</div>
                            @foreach($question->options->where('is_correct', true) as $option)
                                <div>{!! $option->option_text !!}</div>
                            @endforeach
                        </div>
                    @endif

                @else
                    {{-- multiple_choice --}}
                    <div style="margin-bottom: 15px;">
                        <strong style="display: block; margin-bottom: 8px; color: #666; font-size: 13px; text-transform: uppercase;">Варианты ответа:</strong>
                        @foreach($question->options as $option)
                            @php
                                $isUserSelected = in_array($option->id, $detail['user_selected_option_ids']);
                                $isCorrectOption = $option->is_correct;
                                $optionClass = '';
                                
                                if ($isUserSelected && $isCorrectOption) {
                                    $optionClass = 'selected correct';
                                } elseif ($isUserSelected && !$isCorrectOption) {
                                    $optionClass = 'selected incorrect';
                                } elseif ($isCorrectOption) {
                                    $optionClass = 'correct';
                                } elseif ($isUserSelected) {
                                    $optionClass = 'selected';
                                }
                            @endphp

                            <div class="option {{ $optionClass }}">
                                <div class="option-label">
                                    <span class="option-radio">
                                        <input type="checkbox" disabled {{ $isUserSelected ? 'checked' : '' }}>
                                    </span>
                                    <span class="option-text">
                                        {{ $option->option_text }}
                                        @if($isUserSelected)
                                            <span class="option-badge user-answer">Выбран студентом</span>
                                        @endif
                                        @if($isCorrectOption && !$isUserSelected)
                                            <span class="option-badge correct-answer">Должен быть выбран</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
</body>
</html>
