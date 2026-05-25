@extends('layout')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/trix@2.1.16/dist/trix.min.css" rel="stylesheet">
    <style>
        /* ── PAGE SHELL ── */
        .attempt-shell {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1.5rem 4rem;
        }

        body {
            margin: 0;
            padding: 0;
        }

        /* ── BACK BUTTON ── */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-text-secondary);
            text-decoration: none;
            padding: 7px 14px;
            border-radius: var(--r-full);
            border: 1.5px solid var(--color-border);
            background: var(--color-surface);
            transition: var(--transition);
            margin-bottom: 1.75rem;
        }

        .back-link:hover {
            border-color: var(--teal-400);
            color: var(--teal-700);
            background: var(--teal-50);
        }

        /* ── HERO HEADER ── */
        .attempt-hero {
            background: linear-gradient(135deg, var(--teal-700) 0%, var(--sky-700) 100%);
            border-radius: var(--r-2xl);
            padding: 2rem 2.25rem;
            color: #fff;
            margin-bottom: 1.75rem;
            position: relative;
            overflow: hidden;
        }

        .attempt-hero::before {
            content: '';
            position: absolute;
            right: -50px;
            top: -50px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .07);
            pointer-events: none;
        }

        .attempt-hero::after {
            content: '';
            position: absolute;
            right: 100px;
            bottom: -70px;
            width: 170px;
            height: 170px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .04);
            pointer-events: none;
        }

        .attempt-hero__eyebrow {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: .7;
            margin-bottom: 6px;
        }

        .attempt-hero__title {
            font-family: var(--font-display);
            font-size: 26px;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        /* ── STATS ROW IN HERO ── */
        .attempt-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 10px;
            position: relative;
            z-index: 1;
        }

        .attempt-meta-item {
            background: rgba(255, 255, 255, .12);
            backdrop-filter: blur(6px);
            border-radius: var(--r-lg);
            padding: 12px 14px;
        }

        .attempt-meta-item__label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            opacity: .7;
            margin-bottom: 4px;
        }

        .attempt-meta-item__value {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.2;
        }

        .attempt-meta-item__value.score-high {
            color: #a5f3ca;
        }

        .attempt-meta-item__value.score-mid {
            color: #fde68a;
        }

        .attempt-meta-item__value.score-low {
            color: #fca5a5;
        }

        /* ── SECTION HEADING ── */
        .answers-heading {
            font-size: 18px;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 1.25rem;
        }

        /* ── QUESTION CARD ── */
        .q-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--r-xl);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
            overflow: hidden;
            margin-bottom: 1rem;
            transition: box-shadow var(--transition);
        }

        .q-card:hover {
            box-shadow: var(--shadow-md);
        }

        /* ── CARD HEADER ── */
        .q-card__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--color-border);
            background: var(--gray-50);
        }

        .q-card__header-left {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            flex: 1;
            min-width: 0;
        }

        .q-badge {
            width: 32px;
            height: 32px;
            border-radius: var(--r-md);
            background: linear-gradient(135deg, var(--teal-500), var(--sky-500));
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .q-card__meta {
            flex: 1;
            min-width: 0;
        }

        .q-card__number {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--color-text-muted);
            margin-bottom: 3px;
        }

        .q-card__text {
            font-size: 15px;
            font-weight: 600;
            color: var(--gray-800);
            line-height: 1.5;
        }

        .q-type-chip {
            display: inline-flex;
            align-items: center;
            margin-top: 6px;
            padding: 3px 10px;
            border-radius: var(--r-full);
            font-size: 11px;
            font-weight: 700;
            background: var(--sky-50);
            color: var(--sky-700);
        }

        /* ── STATUS BADGE ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 14px;
            border-radius: var(--r-full);
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .status-badge.correct {
            background: var(--green-50);
            color: var(--green-600);
            border: 1.5px solid var(--green-100);
        }

        .status-badge.incorrect {
            background: #ffebee;
            color: #c62828;
            border: 1.5px solid #ffcdd2;
        }

        .status-badge.pending {
            background: #fff8e1;
            color: #e65100;
            border: 1.5px solid #ffecb3;
        }

        .status-badge.empty {
            background: var(--gray-100);
            color: var(--gray-500);
            border: 1.5px solid var(--gray-200);
        }

        /* ── CARD BODY ── */
        .q-card__body {
            padding: 1.25rem 1.5rem;
        }

        .answer-section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--color-text-muted);
            margin-bottom: 8px;
        }

        /* ── TEXT ANSWERS ── */
        .text-answer-box {
            padding: 13px 16px;
            border: 1.5px solid var(--color-border);
            border-radius: var(--r-lg);
            background: var(--gray-50);
            font-size: 14.5px;
            color: var(--gray-700);
            line-height: 1.6;
        }

        .text-answer-box.correct {
            background: var(--green-50);
            border-color: var(--green-400);
            color: #1b5e20;
        }

        .text-answer-box.incorrect {
            background: #ffebee;
            border-color: #ef9a9a;
            color: #b71c1c;
        }

        .rich-text-answer-box {
            padding: 13px 16px;
            border: 1.5px solid var(--color-border);
            border-radius: var(--r-lg);
            background: var(--color-surface);
            font-size: 14.5px;
            color: var(--gray-700);
            line-height: 1.7;
        }

        .rich-text-answer-box.correct {
            background: var(--green-50);
            border-color: var(--green-400);
        }

        .rich-text-answer-box.incorrect {
            background: #ffebee;
            border-color: #ef9a9a;
        }

        .no-answer-box {
            padding: 13px 16px;
            background: var(--gray-100);
            border-radius: var(--r-lg);
            color: var(--color-text-muted);
            font-style: italic;
            font-size: 14px;
        }

        /* ── CORRECT ANSWERS HINT ── */
        .correct-answers-panel {
            margin-top: 12px;
            padding: 12px 14px;
            background: var(--teal-50);
            border-left: 3px solid var(--teal-500);
            border-radius: 0 var(--r-md) var(--r-md) 0;
        }

        .correct-answers-panel__title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--teal-700);
            margin-bottom: 6px;
        }

        .correct-answers-panel p {
            font-size: 13.5px;
            color: var(--teal-800);
            line-height: 1.5;
        }

        /* ── OPTION ITEMS ── */
        .option-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 11px 14px;
            border: 1.5px solid var(--color-border);
            border-radius: var(--r-lg);
            background: var(--color-surface);
            margin-bottom: 8px;
            transition: var(--transition);
        }

        .option-item:last-child {
            margin-bottom: 0;
        }

        .option-item.correct {
            background: var(--green-50);
            border-color: var(--green-400);
        }

        .option-item.selected-correct {
            background: #c8e6c9;
            border-color: #388e3c;
        }

        .option-item.selected-incorrect {
            background: #ffebee;
            border-color: #ef9a9a;
        }

        .option-item__check {
            flex-shrink: 0;
            margin-top: 1px;
        }

        .option-item__text {
            flex: 1;
            font-size: 14px;
            color: var(--gray-700);
            line-height: 1.5;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        .micro-badge {
            display: inline-flex;
            padding: 2px 8px;
            border-radius: var(--r-full);
            font-size: 11px;
            font-weight: 700;
        }

        .micro-badge.user {
            background: #fff3cd;
            color: #856404;
        }

        .micro-badge.should {
            background: var(--green-50);
            color: var(--green-600);
            border: 1px solid var(--green-100);
        }

        /* ── TEACHER GRADING ── */
        .grading-panel {
            margin-top: 14px;
            padding: 14px;
            background: var(--gray-50);
            border: 1.5px solid var(--color-border);
            border-radius: var(--r-lg);
        }

        .grading-panel__title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--color-text-muted);
            margin-bottom: 10px;
        }

        .grading-radios {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .grade-radio-label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: var(--r-full);
            border: 1.5px solid var(--color-border);
            background: var(--color-surface);
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-text-secondary);
            transition: var(--transition);
        }

        .grade-radio-label:hover {
            border-color: var(--teal-400);
            color: var(--teal-700);
            background: var(--teal-50);
        }

        .grade-radio-label input[type="radio"] {
            accent-color: var(--teal-500);
        }

        .grade-radio-label input[type="radio"]:checked+span {
            color: var(--teal-700);
        }

        .grade-radio-label:has(input:checked) {
            border-color: var(--teal-500);
            background: var(--teal-50);
            color: var(--teal-700);
        }

        /* ── SUBMIT BUTTON ── */
        .grade-submit-wrap {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--color-border);
        }

        .grade-submit-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            background: var(--teal-500);
            color: #fff;
            border: none;
            border-radius: var(--r-full);
            font-size: 14px;
            font-weight: 700;
            font-family: var(--font-body);
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(0, 181, 165, .3);
            transition: var(--transition);
        }

        .grade-submit-btn:hover {
            background: var(--teal-600);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 181, 165, .4);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 640px) {
            .attempt-hero {
                padding: 1.5rem;
            }

            .attempt-hero__title {
                font-size: 20px;
            }

            .attempt-meta-grid {
                grid-template-columns: 1fr 1fr;
            }

            .q-card__header {
                flex-direction: column;
            }

            .status-badge {
                align-self: flex-start;
            }
        }
    </style>
@endsection

@section('content')
    <div class="attempt-shell">

        {{-- Back button --}}
        <a href="{{ route('tests.view', $test) }}" class="back-link">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            К тесту
        </a>

        {{-- Hero header --}}
        <div class="attempt-hero">
            <div class="attempt-hero__eyebrow">Детали попытки</div>
            <h1 class="attempt-hero__title">{{ $test->title }}</h1>

            <div class="attempt-meta-grid">
                <div class="attempt-meta-item">
                    <div class="attempt-meta-item__label">Студент</div>
                    <div class="attempt-meta-item__value">{{ $user->name }}</div>
                </div>
                <div class="attempt-meta-item">
                    <div class="attempt-meta-item__label">Попытка</div>
                    <div class="attempt-meta-item__value">#{{ $attempt->attempt_number }}</div>
                </div>
                <div class="attempt-meta-item">
                    <div class="attempt-meta-item__label">Результат</div>
                    <div
                        class="attempt-meta-item__value {{ $attempt->score >= 70 ? 'score-high' : ($attempt->score >= 50 ? 'score-mid' : 'score-low') }}">
                        {{ $attempt->score }}%
                    </div>
                </div>
                <div class="attempt-meta-item">
                    <div class="attempt-meta-item__label">Завершено</div>
                    <div class="attempt-meta-item__value">{{ $attempt->ended_at->format('d.m.Y H:i') }}</div>
                </div>
                <div class="attempt-meta-item">
                    <div class="attempt-meta-item__label">Время</div>
                    <div class="attempt-meta-item__value">
                        {{ \App\Helpers\TimeFormatter::formatMinutes($attempt->started_at->diffInMinutes($attempt->ended_at)) }}
                    </div>
                </div>
                <div class="attempt-meta-item">
                    <div class="attempt-meta-item__label">Курс</div>
                    <div class="attempt-meta-item__value" style="font-size:14px;">{{ $course->title }}</div>
                </div>
            </div>
        </div>

        {{-- Questions --}}
        <div class="answers-heading">Ответы студента</div>

        @hasanyrole('admin|teacher')
            <form method="POST" action="{{ route('test-attempts.grade-rich-text', $attempt) }}">
                @csrf
            @endhasanyrole

            @foreach ($questionDetails as $index => $detail)
                @php
                    $question = $detail['question'];
                    $isCorrect = $detail['is_correct'];
                    $hasAnswer = $detail['user_answer_text'] || count($detail['user_selected_option_ids']) > 0;
                    $isManuallyGraded = $detail['is_manually_graded'] ?? false;

                    if ($question->question_type === 'rich_text_answer' && $hasAnswer && !$isManuallyGraded) {
                        $badgeClass = 'pending';
                        $badgeText = '⏳ Ожидает проверки';
                    } elseif ($isCorrect) {
                        $badgeClass = 'correct';
                        $badgeText = '✓ Правильно';
                    } elseif ($hasAnswer) {
                        $badgeClass = 'incorrect';
                        $badgeText = '✗ Неправильно';
                    } else {
                        $badgeClass = 'empty';
                        $badgeText = '— Не ответил';
                    }

                    $typeLabel = match ($question->question_type) {
                        'single_choice' => 'Один ответ',
                        'multiple_choice' => 'Несколько ответов',
                        'rich_text_answer' => 'Развёрнутый ответ',
                        'fill_in_dropdown' => 'Выпадающий список',
                        'fill_in_the_blank' => 'Заполнение пропуска',
                        default => 'Текстовый ответ',
                    };
                @endphp

                <div class="q-card">

                    {{-- Card header --}}
                    <div class="q-card__header">
                        <div class="q-card__header-left">
                            <div class="q-badge">{{ $index + 1 }}</div>
                            <div class="q-card__meta">
                                <div class="q-card__number">Вопрос {{ $index + 1 }}</div>
                                <div class="q-card__text">{{ strip_tags($question->question_text) }}</div>
                                <span class="q-type-chip">{{ $typeLabel }}</span>
                            </div>
                        </div>
                        <span class="status-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                    </div>

                    {{-- Card body --}}
                    <div class="q-card__body">

                        @if ($question->question_type === 'short_answer')
                            <div class="answer-section-label">Ответ студента</div>
                            @if ($detail['user_answer_text'])
                                <div class="text-answer-box {{ $isCorrect ? 'correct' : 'incorrect' }}">
                                    {{ $detail['user_answer_text'] }}
                                </div>
                            @else
                                <div class="no-answer-box">Студент не дал ответ</div>
                            @endif

                            @if (!$isCorrect)
                                <div class="correct-answers-panel">
                                    <div class="correct-answers-panel__title">Правильные ответы</div>
                                    @foreach ($question->options->where('is_correct', true) as $option)
                                        <p>• {{ $option->option_text }}</p>
                                    @endforeach
                                </div>
                            @endif
                        @elseif($question->question_type === 'rich_text_answer')
                            <div class="answer-section-label">Ответ студента</div>
                            @if ($detail['user_answer_text'])
                                <div
                                    class="rich-text-answer-box {{ $isManuallyGraded ? ($isCorrect ? 'correct' : 'incorrect') : '' }}">
                                    {!! $detail['user_answer_text'] !!}
                                </div>
                            @else
                                <div class="no-answer-box">Студент не дал ответ</div>
                            @endif

                            @if ($detail['user_answer_text'])
                                @hasanyrole('admin|teacher')
                                    <div class="grading-panel">
                                        <div class="grading-panel__title">Оценка учителя</div>
                                        <div class="grading-radios">
                                            <label class="grade-radio-label">
                                                <input type="radio" name="grades[{{ $question->id }}]" value="correct"
                                                    {{ $isManuallyGraded && $isCorrect ? 'checked' : '' }}>
                                                <span>✓ Засчитать как правильный</span>
                                            </label>
                                            <label class="grade-radio-label">
                                                <input type="radio" name="grades[{{ $question->id }}]" value="incorrect"
                                                    {{ $isManuallyGraded && !$isCorrect ? 'checked' : '' }}>
                                                <span>✗ Отметить как неправильный</span>
                                            </label>
                                        </div>
                                    </div>
                                @endhasanyrole
                            @endif
                        @elseif(in_array($question->question_type, ['single_choice', 'multiple_choice']))
                            {{-- single_choice / multiple_choice --}}
                            <div class="answer-section-label">Варианты ответа</div>
                            @foreach ($question->options as $option)
                                @php
                                    $isUserSelected = in_array($option->id, $detail['user_selected_option_ids']);
                                    $isCorrectOption = $option->is_correct;

                                    if ($isUserSelected && $isCorrectOption) {
                                        $cls = 'selected-correct';
                                    } elseif ($isUserSelected && !$isCorrectOption) {
                                        $cls = 'selected-incorrect';
                                    } elseif ($isCorrectOption) {
                                        $cls = 'correct';
                                    } else {
                                        $cls = '';
                                    }
                                @endphp
                                <div class="option-item {{ $cls }}">
                                    <div class="option-item__check">
                                        <input type="checkbox" disabled {{ $isUserSelected ? 'checked' : '' }}
                                            style="width:16px;height:16px;accent-color:var(--teal-500);margin-top:2px;">
                                    </div>
                                    <div class="option-item__text">
                                        {{ $option->option_text }}
                                        @if ($isUserSelected)
                                            <span class="micro-badge user">Выбран студентом</span>
                                        @endif
                                        @if ($isCorrectOption && !$isUserSelected)
                                            <span class="micro-badge should">Должен быть выбран</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @elseif($question->question_type === 'fill_in_dropdown')
                            <div class="answer-section-label">Ответ студента (выпадающий список)</div>
                            @php
                                // Группируем варианты по blank_id
                                $byBlank = [];
                                foreach ($question->options as $opt) {
                                    $decoded = json_decode($opt->option_text, true);
                                    if (!is_array($decoded)) {
                                        continue;
                                    }
                                    $bid = $decoded['blank_id'];
                                    $byBlank[$bid][] = [
                                        'id' => $opt->id,
                                        'text' => $decoded['text'] ?? '',
                                        'is_correct' => (bool) $opt->is_correct,
                                        'selected' => in_array($opt->id, $detail['user_selected_option_ids']),
                                    ];
                                }
                                ksort($byBlank);
                            @endphp

                            @if (empty($detail['user_selected_option_ids']))
                                <div class="no-answer-box">Студент не дал ответ</div>
                            @else
                                @foreach ($byBlank as $blankId => $opts)
                                    @php
                                        $selected = collect($opts)->firstWhere('selected', true);
                                        $correct = collect($opts)->firstWhere('is_correct', true);
                                        $isBlankCorrect = $selected && $selected['is_correct'];
                                    @endphp
                                    <div
                                        class="option-item {{ $isBlankCorrect ? 'selected-correct' : ($selected ? 'selected-incorrect' : '') }}">
                                        <div class="option-item__text">
                                            <span
                                                style="color:var(--color-text-muted);font-size:12px;font-weight:700;min-width:70px;">
                                                Пропуск {{ $blankId }}:
                                            </span>
                                            @if ($selected)
                                                <strong>{{ $selected['text'] }}</strong>
                                                @if (!$isBlankCorrect && $correct)
                                                    <span class="micro-badge should">Правильно:
                                                        {{ $correct['text'] }}</span>
                                                @endif
                                            @else
                                                <em style="color:var(--color-text-muted)">не выбрано</em>
                                                @if ($correct)
                                                    <span class="micro-badge should">Правильно:
                                                        {{ $correct['text'] }}</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @elseif($question->question_type === 'fill_in_the_blank')
                            {{-- fill_in_the_blank --}}
                            <div class="answer-section-label">Ответ студента (пропуски)</div>
                            @php
                                // user_answer_text может быть JSON-массивом вида [{"blank_id":1,"text":"..."}]
                                $userBlanks = [];
                                if ($detail['user_answer_text']) {
                                    $decoded = json_decode($detail['user_answer_text'], true);
                                    if (is_array($decoded)) {
                                        foreach ($decoded as $b) {
                                            $userBlanks[$b['blank_id']] = $b['text'] ?? '';
                                        }
                                    }
                                }

                                // Правильные ответы из options — option_text тоже JSON
                                $correctBlanks = [];
                                foreach ($question->options->where('is_correct', true) as $opt) {
                                    $decoded = json_decode($opt->option_text, true);
                                    if (is_array($decoded) && isset($decoded['blank_id'])) {
                                        $correctBlanks[$decoded['blank_id']][] = $decoded['text'] ?? '';
                                    }
                                }
                            @endphp

                            @if (empty($userBlanks))
                                <div class="no-answer-box">Студент не дал ответ</div>
                            @else
                                @foreach ($userBlanks as $blankId => $userText)
                                    @php
                                        $correct = $correctBlanks[$blankId] ?? [];
                                        $isBlankCorrect = collect($correct)->contains(
                                            fn($c) => mb_strtolower(trim($c)) === mb_strtolower(trim($userText)),
                                        );
                                    @endphp
                                    <div class="option-item {{ $isBlankCorrect ? 'selected-correct' : 'selected-incorrect' }}"
                                        style="margin-bottom:8px;">
                                        <div class="option-item__text">
                                            <span
                                                style="color:var(--color-text-muted);font-size:12px;font-weight:700;min-width:70px;">
                                                Пропуск {{ $blankId }}:
                                            </span>
                                            <strong>{{ $userText ?: '(пусто)' }}</strong>
                                            @if (!$isBlankCorrect && count($correct))
                                                <span class="micro-badge should">
                                                    Правильно: {{ implode(' / ', $correct) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @else
                            {{-- fallback --}}
                            <div class="answer-section-label">Ответ студента</div>
                            @if ($detail['user_answer_text'])
                                <div class="text-answer-box">{{ $detail['user_answer_text'] }}</div>
                            @else
                                <div class="no-answer-box">Студент не дал ответ</div>
                            @endif
                        @endif

                    </div>
                </div>
            @endforeach

            @hasanyrole('admin|teacher')
                <div class="grade-submit-wrap">
                    <button type="submit" class="grade-submit-btn">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Сохранить оценки развёрнутых ответов
                    </button>
                </div>
            </form>
        @endhasanyrole

    </div>
@endsection
