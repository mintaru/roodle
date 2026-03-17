<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Question;
use App\Models\TemporaryAnswer;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use App\Models\UserTestExtraAttempt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    // Список тестов
    public function index()
    {
        $tests = Test::orderBy('created_at', 'desc')->get();

        return view('layout')->with('content', view('test_list', ['tests' => $tests]));
    }

    // Форма создания нового теста для конкретного курса
    public function create(Course $course)
    {
        return view('layout', [
            'content' => view('test_create_form', ['course' => $course]),
        ]);
    }

    public function store(Request $request, Course $course)
    {
        // Валидируем данные
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_attempts' => 'nullable|integer|min:1', // если не отмечен "неограниченно"
            'unlimited_attempts' => 'nullable|boolean',
            'time_limit' => 'nullable|integer|min:0',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after:period_start',
            'randomize_questions' => 'nullable|boolean',
            'display_mode' => 'required|in:single_page,per_question,paged',
        ]);
        $validatedData['time_limit'] = $request->input('time_limit', 0);

        $validatedData['period_start'] = $request->period_start
        ? Carbon::createFromFormat(
            'Y-m-d\TH:i',
            $request->period_start,
            'Asia/Krasnoyarsk'
        )->utc()
        : null;

        $validatedData['period_end'] = $request->period_end
            ? Carbon::createFromFormat(
                'Y-m-d\TH:i',
                $request->period_end,
                'Asia/Krasnoyarsk'
            )->utc()
            : null;

        // Определяем max_attempts
        if ($request->has('unlimited_attempts')) {
            $validatedData['max_attempts'] = 0; // 0 = неограниченно
        } else {
            // берем введённое число или 1 по умолчанию
            $validatedData['max_attempts'] = $request->input('max_attempts', 1);
        }

        // Настройки отображения теста
        $validatedData['randomize_questions'] = $request->has('randomize_questions');
        $validatedData['display_mode'] = $request->input('display_mode', 'single_page');

        // Создаём тест
        $test = $course->tests()->create($validatedData);

        return redirect()->route('tests.show', $test);
    }

    public function attempts($id)
    {
        $test = Test::with('attempts')->findOrFail($id);

        return view('admin.tests.attempts', compact('test'));
    }

    // Отображение теста с вопросами
    public function show(Test $test)
    {
        abort_if(! $test->isAvailable(), 404);

        $test->load('questions.options');

        // Выбираем все вопросы из банка
        $allQuestions = \App\Models\Question::with('options')->get();

        return view('layout', [
            'content' => view('test_show', [
                'test' => $test,
                'allQuestions' => $allQuestions, // передаём в шаблон
            ]),
        ]);
    }

    public function view(Test $test)
    {
        abort_if(! $test->isAvailable(), 404);

        $user = Auth::user();

        // Количество попыток пользователя
        $userAttemptsCount = $test->attempts()
            ->where('user_id', $user->id)
            ->whereNotNull('ended_at')
            ->count();
        $userAttempts = $test->attempts()->where('user_id', $user->id)->whereNotNull('ended_at')->get();

        // Максимальное количество попыток для пользователя (с учётом дополнительных)
        $maxAttemptsForUser = $test->getMaxAttemptsForUser($user->id);
        $isUnlimited = ($maxAttemptsForUser === 0);

        // Оставшиеся попытки
        $remaining = $isUnlimited
            ? '∞'
            : max(0, $maxAttemptsForUser - $userAttemptsCount);

        // Проверяем, есть ли активная попытка (где ended_at == null)
        $hasActiveAttempt = $test->attempts()
            ->where('user_id', $user->id)
            ->whereNull('ended_at')
            ->exists();

        $activeAttempt = null;
        if ($hasActiveAttempt) {
            $activeAttempt = $test->attempts()
                ->where('user_id', $user->id)
                ->whereNull('ended_at')
                ->first();
        }

        return view('tests.view', compact(
            'test',
            'userAttemptsCount',
            'userAttempts',
            'remaining',
            'hasActiveAttempt',
            'activeAttempt',
            'maxAttemptsForUser',
            'isUnlimited'
        ));
    }

    /**
     * Сохраняет новый вопрос для теста.
     */
    public function storeQuestion(Request $request, Test $test)
    {
        // Валидация
        $validatedData = $request->validate([
            'question_text' => 'required_unless:question_type,fill_in_dropdown|string',
            'question_type' => 'required|in:single_choice,multiple_choice,short_answer,rich_text_answer,fill_in_dropdown',
            'options' => [
                'required_if:question_type,single_choice',
                'required_if:question_type,multiple_choice',
                'nullable',
                'array',
                'min:2',
            ],
            'options.*' => [
                'required_if:question_type,single_choice',
                'required_if:question_type,multiple_choice',
                'nullable',
                'string',
            ],
            'correct_option' => 'required_if:question_type,single_choice|integer',
            'correct_options' => 'required_if:question_type,multiple_choice|array',
            'correct_options.*' => 'integer',
            'correct_answers' => 'required_if:question_type,short_answer|array|min:1',
            'correct_answers.*' => 'required_if:question_type,short_answer|string|min:1',
            'case_insensitive' => 'nullable|in:0,1',
            'fill_text' => 'required_if:question_type,fill_in_dropdown|string',
            'dropdown_options' => 'required_if:question_type,fill_in_dropdown|array',
            'dropdown_options.*' => 'array',
            'dropdown_correct' => 'required_if:question_type,fill_in_dropdown|array',
        ]);

        // Создаём вопрос
        if ($validatedData['question_type'] === 'fill_in_dropdown') {
            $question = Question::create([
                'question_text' => $validatedData['fill_text'],
                'question_type' => 'fill_in_dropdown',
            ]);
            // Для каждого пропуска создаём опции (blank_id, текст, правильность)
            foreach ($validatedData['dropdown_options'] as $blankId => $optionsArr) {
                foreach ($optionsArr as $idx => $optionText) {
                    $isCorrect = (isset($validatedData['dropdown_correct'][$blankId]) && $validatedData['dropdown_correct'][$blankId] == $idx);
                    $question->options()->create([
                        'option_text' => json_encode([
                            'blank_id' => $blankId,
                            'text' => $optionText,
                        ], JSON_UNESCAPED_UNICODE),
                        'is_correct' => $isCorrect,
                        'case_insensitive' => false,
                    ]);
                }
            }
        } elseif ($validatedData['question_type'] === 'short_answer') {
            $question = Question::create([
                'question_text' => $validatedData['question_text'],
                'question_type' => $validatedData['question_type'],
            ]);
            // Для текстовых ответов создаём опции с правильными ответами
            $caseInsensitive = ($validatedData['case_insensitive'] ?? 0) == 1;
            foreach ($validatedData['correct_answers'] as $answer) {
                if (trim($answer) !== '') { // Пропускаем пустые ответы
                    $question->options()->create([
                        'option_text' => trim($answer),
                        'is_correct' => true,
                        'case_insensitive' => $caseInsensitive,
                    ]);
                }
            }
        } elseif (in_array($validatedData['question_type'], ['single_choice', 'multiple_choice'], true)) {
            $question = Question::create([
                'question_text' => $validatedData['question_text'],
                'question_type' => $validatedData['question_type'],
            ]);
            // Сохраняем варианты и отмечаем правильные для вопросов с выбором ответа
            foreach ($validatedData['options'] as $key => $optionText) {
                $isCorrect = false;
                if ($validatedData['question_type'] === 'single_choice') {
                    $isCorrect = ($key == $validatedData['correct_option']);
                } else {
                    $isCorrect = in_array($key, $validatedData['correct_options']);
                }
                $question->options()->create([
                    'option_text' => $optionText,
                    'is_correct' => $isCorrect,
                    'case_insensitive' => false, // для множественных выборов это не используется
                ]);
            }
        } elseif ($validatedData['question_type'] === 'rich_text_answer') {
            $question = Question::create([
                'question_text' => $validatedData['question_text'],
                'question_type' => $validatedData['question_type'],
            ]);
            // Для rich_text_answer не создаём опций
        }

        // Привязываем вопрос к тесту
        $test->questions()->attach($question->id);

        return back()->with('success', 'Вопрос успешно добавлен!');
    }

    public function addFromBank(Request $request, Test $test)
    {
        $validatedData = $request->validate([
            'question_id' => 'required|exists:questions,id',
        ]);

        $test->questions()->attach($validatedData['question_id']);

        return back()->with('success', 'Вопрос добавлен из банка!');
    }

    /**
     * Обновление разбиения вопросов теста по страницам (для режима display_mode = paged).
     */
    public function updateLayout(Request $request, Test $test)
    {
        // Только преподаватели/админы могут менять структуру теста
        if (! Auth::user()->can('edit courses')) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'pages' => 'required|array',
            'pages.*' => 'nullable|integer|min:1',
        ]);

        $pages = $data['pages'] ?? [];

        foreach ($pages as $questionId => $pageNumber) {
            $page = max(1, (int) $pageNumber);
            $test->questions()->updateExistingPivot($questionId, [
                'page_number' => $page,
            ]);
        }

        return back()->with('success', 'Разбиение по страницам сохранено.');
    }

    public function result(Test $test, Request $request)
    {
        abort_if(! $test->isAvailable(), 404);

        $user = Auth::user();

        // Получаем активную попытку из БД
        $attempt = $test->attempts()
            ->where('user_id', $user->id)
            ->whereNull('ended_at')
            ->first();

        // Если активной попытки нет, перенаправляем
        if (! $attempt) {
            return redirect()->route('tests.view', $test)
                ->with('error', 'Активная попытка не найдена.');
        }

        // Подсчитываем результат попытки (rich_text ответы пока не засчитываются автоматически)
        $scoreData = $this->calculateScoreForAttempt($test, $attempt);
        $score = $scoreData['score'];

        // Обновляем активную попытку и сохраняем ответы
        DB::transaction(function () use ($attempt, $score) {
            $attempt->update([
                'score' => round($score),
                'ended_at' => now(),
            ]);

            // Помечаем все ответы текущей попытки как неактивные
            TemporaryAnswer::where('test_attempt_id', $attempt->id)
                ->update(['is_active' => false]);
        });

        return view('layout', [
            'content' => view('test_result', [
                'test' => $test,
                'score' => round($score),
                'correctAnswers' => $scoreData['correctAnswers'],
                'totalQuestions' => $scoreData['totalQuestions'],
            ]),
        ]);
    }

    /**
     * Удаляет вопрос из теста.
     */
    public function removeQuestion(Test $test, Question $question)
    {
        // Проверяем, что вопрос действительно в этом тесте
        if ($test->questions()->where('question_id', $question->id)->exists()) {
            // Отсоединяем вопрос от теста
            $test->questions()->detach($question->id);

            return back()->with('success', 'Вопрос успешно удален из теста!');
        }

        return back()->with('error', 'Вопрос не найден в этом тесте.');
    }

    /**
     * Синхронизирует таймер между устройствами.
     * Возвращает оставшееся время в секундах.
     */
    public function timerSync(Test $test)
    {
        $user = Auth::user();

        // Получаем время начала из сессии
        $startedAt = session("test_{$test->id}_started_at");

        if (! $startedAt) {
            return response()->json(['time_left' => $test->time_limit * 60], 400);
        }

        // Рассчитываем оставшееся время на основе серверного времени
        $now = now();
        $elapsedSeconds = $now->diffInSeconds($startedAt);
        $timeLimitSeconds = $test->time_limit * 60;
        $timeLeft = max(0, $timeLimitSeconds - $elapsedSeconds);

        return response()->json([
            'time_left' => $timeLeft,
            'server_time' => $now->timestamp,
        ]);
    }

    /**
     * Отображает результаты теста - кто из учеников сейчас проходит, их статусы и результаты
     */
    public function results(Test $test)
    {
        // Проверяем, что пользователь с правом редактирования курсов (учитель/админ)
        if (! Auth::user()->can('edit courses')) {
            abort(403, 'Unauthorized');
        }

        // Получаем все группы, связанные с курсом теста
        $course = $test->course;

        // Получаем всех уникальных пользователей из групп курса
        $usersInCourse = $course->groups()
            ->with('users')
            ->get()
            ->pluck('users')
            ->flatten()
            ->unique('id')
            ->values();

        // Подготавливаем данные для каждого пользователя
        $studentsData = [];

        foreach ($usersInCourse as $user) {
            // Все попытки пользователя (завершённые и текущие)
            $attempts = $test->attempts()
                ->where('user_id', $user->id)
                ->orderBy('attempt_number', 'asc')
                ->get();

            // Определяем, есть ли активная попытка
            $activeAttempt = $attempts->where('ended_at', null)->first();
            $completedAttempts = $attempts->where('ended_at', '!=', null);

            // Какой номер попытки сейчас/будет
            $currentAttemptNumber = $attempts->count() + 1;

            // Статус
            $status = 'не начинали';
            $minutesSpent = null;

            if ($activeAttempt) {
                $status = 'в процессе';
                // Считаем минуты, прошедшие с начала попытки
                if ($activeAttempt->started_at) {
                    $minutesSpent = $activeAttempt->started_at->diffInMinutes(now());
                }
            } elseif ($completedAttempts->count() > 0) {
                $status = 'завершили';
            }

            $studentsData[] = [
                'user' => $user,
                'status' => $status,
                'current_attempt_number' => $currentAttemptNumber,
                'minutes_spent' => $minutesSpent,
                'active_attempt' => $activeAttempt,
                'completed_attempts' => $completedAttempts,
            ];
        }

        // Сортируем по статусу и имени
        usort($studentsData, function ($a, $b) {
            $statusOrder = ['в процессе' => 0, 'завершили' => 1, 'не начинали' => 2];
            if ($statusOrder[$a['status']] !== $statusOrder[$b['status']]) {
                return $statusOrder[$a['status']] <=> $statusOrder[$b['status']];
            }

            return $a['user']->name <=> $b['user']->name;
        });

        return view('tests.results', compact('test', 'studentsData', 'course'));
    }

    /**
     * Просмотр деталей попытки ученика - его ответы
     */
    public function viewAttemptDetails(TestAttempt $attempt)
    {
        // Проверяем, что пользователь имеет право просматривать (учитель/админ)
        if (! Auth::user()->can('edit courses')) {
            abort(403, 'Unauthorized');
        }

        $test = $attempt->test;
        $user = $attempt->user;
        $course = $test->course;

        // Загружаем все вопросы теста с опциями
        $test->load(['questions' => function ($q) {
            $q->with('options');
        }]);

        // Получаем все ответы студента для этой попытки
        $studentAnswers = TemporaryAnswer::where('test_attempt_id', $attempt->id)
            ->get()
            ->groupBy('question_id');

        // Подготавливаем данные для каждого вопроса
        $questionDetails = [];

        foreach ($test->questions as $question) {
            $answers = $studentAnswers->get($question->id, collect());

            // Определяем, правильный ли ответ
            $isCorrect = false;
            $userAnswerText = '';
            $userSelectedOptions = [];
            $isManuallyGraded = false;

            if ($question->question_type === 'short_answer') {
                // Для текстовых ответов
                $answer = $answers->first();
                if ($answer && $answer->answer_text) {
                    $userAnswerText = $answer->answer_text;

                    // Проверяем правильность
                    $correctOptions = $question->options->where('is_correct', true);
                    foreach ($correctOptions as $option) {
                        $correctText = trim($option->option_text);

                        if ($option->case_insensitive) {
                            if (strtolower(preg_replace('/\s+/', '', $userAnswerText)) ===
                                strtolower(preg_replace('/\s+/', '', $correctText))) {
                                $isCorrect = true;
                                break;
                            }
                        } else {
                            if ($userAnswerText === $correctText) {
                                $isCorrect = true;
                                break;
                            }
                        }
                    }
                }
            } elseif ($question->question_type === 'rich_text_answer') {
                // Для развёрнутых ответов
                $answer = $answers->first();
                if ($answer && $answer->answer_text) {
                    $userAnswerText = $answer->answer_text;
                    $isManuallyGraded = (bool) $answer->is_manually_graded;

                    // Для развёрнутых ответов учитываем только ручную проверку учителя
                    if ($answer->is_manually_graded && ! is_null($answer->is_correct_manual)) {
                        $isCorrect = (bool) $answer->is_correct_manual;
                    }
                }
            } else {
                // Для множественного выбора
                $userSelectedOptions = $answers->pluck('option_id')->filter()->toArray();
                $correctOptionIds = $question->options
                    ->where('is_correct', true)
                    ->pluck('id')
                    ->toArray();

                if ($question->question_type === 'single_choice') {
                    $isCorrect = (count($userSelectedOptions) === 1 &&
                                 in_array($userSelectedOptions[0], $correctOptionIds));
                } else {
                    // multiple_choice
                    sort($userSelectedOptions);
                    sort($correctOptionIds);
                    $isCorrect = ($userSelectedOptions === $correctOptionIds);
                }
            }

            $questionDetails[] = [
                'question' => $question,
                'user_answer_text' => $userAnswerText,
                'user_selected_option_ids' => $userSelectedOptions,
                'is_correct' => $isCorrect,
                'is_manually_graded' => $isManuallyGraded,
                'answers' => $answers,
            ];
        }

        return view('tests.attempt-details', compact('attempt', 'test', 'user', 'course', 'questionDetails'));
    }

    /**
     * Ручная проверка развёрнутых ответов учителем и пересчёт итогового балла.
     */
    public function gradeRichTextAnswers(Request $request, TestAttempt $attempt)
    {
        // Проверяем права (учитель/админ)
        if (! Auth::user()->can('edit courses')) {
            abort(403, 'Unauthorized');
        }

        $test = $attempt->test;

        $grades = $request->input('grades', []);

        // Обновляем только rich_text-вопросы
        foreach ($grades as $questionId => $value) {
            /** @var \App\Models\TemporaryAnswer|null $answer */
            $answer = TemporaryAnswer::where('test_attempt_id', $attempt->id)
                ->where('question_id', (int) $questionId)
                ->first();

            if (! $answer) {
                continue;
            }

            $isCorrect = $value === 'correct';

            $answer->update([
                'is_manually_graded' => true,
                'is_correct_manual' => $isCorrect,
            ]);
        }

        // Пересчитываем общий балл попытки с учётом ручной проверки
        $scoreData = $this->calculateScoreForAttempt($test, $attempt);
        $attempt->update([
            'score' => round($scoreData['score']),
        ]);

        return redirect()
            ->route('test-attempts.details', $attempt)
            ->with('success', 'Оценки за развёрнутые ответы сохранены.');
    }

    /**
     * Подсчёт результата попытки по всем вопросам.
     * Для rich_text учитываются только ручные оценки учителя.
     */
    protected function calculateScoreForAttempt(Test $test, TestAttempt $attempt): array
    {
        // Загружаем вопросы с вариантами ответов
        $test->load('questions.options');

        // Все ответы по данной попытке (независимо от is_active)
        $tempAnswers = TemporaryAnswer::where('test_attempt_id', $attempt->id)
            ->get()
            ->groupBy('question_id');

        $totalQuestions = $test->questions()->count();
        $correctAnswers = 0;

        foreach ($test->questions as $question) {
            $answers = $tempAnswers->get($question->id, collect());
            $isCorrect = false;

            if ($question->question_type === 'short_answer') {
                $answer = $answers->first();
                $userAnswer = $answer && $answer->answer_text ? trim($answer->answer_text) : '';

                if ($userAnswer !== '') {
                    $correctOptions = $question->options->where('is_correct', true);

                    foreach ($correctOptions as $option) {
                        $correctText = trim($option->option_text);

                        if ($option->case_insensitive) {
                            if (
                                strtolower(preg_replace('/\s+/', '', $userAnswer)) ===
                                strtolower(preg_replace('/\s+/', '', $correctText))
                            ) {
                                $isCorrect = true;
                                break;
                            }
                        } else {
                            if ($userAnswer === $correctText) {
                                $isCorrect = true;
                                break;
                            }
                        }
                    }
                }
            } elseif ($question->question_type === 'rich_text_answer') {
                // Только ручная оценка учителя
                $answer = $answers->first();
                if ($answer && $answer->is_manually_graded && ! is_null($answer->is_correct_manual)) {
                    $isCorrect = (bool) $answer->is_correct_manual;
                }
            } elseif ($question->question_type === 'fill_in_dropdown') {
                // Для каждого blank_id сравниваем выбранный вариант с правильным
                $correct = true;
                // Группируем опции по blank_id
                $optionsByBlank = [];
                foreach ($question->options as $opt) {
                    $data = json_decode($opt->option_text, true);
                    if (! isset($optionsByBlank[$data['blank_id']])) {
                        $optionsByBlank[$data['blank_id']] = [];
                    }
                    $optionsByBlank[$data['blank_id']][] = [
                        'id' => $opt->id,
                        'is_correct' => $opt->is_correct,
                    ];
                }
                // Собираем ответы пользователя: blank_id => option_id
                $userAnswers = [];
                foreach ($answers as $ans) {
                    $userAnswers[$ans->answer_text] = $ans->option_id; // answer_text = blank_id
                }
                foreach ($optionsByBlank as $blankId => $opts) {
                    $correctOption = collect($opts)->firstWhere('is_correct', true);
                    if (! $correctOption || ! isset($userAnswers[$blankId]) || $userAnswers[$blankId] != $correctOption['id']) {
                        $correct = false;
                        break;
                    }
                }
                $isCorrect = $correct;
            } else {
                // single_choice / multiple_choice
                $userOptionIds = $answers
                    ->pluck('option_id')
                    ->filter()
                    ->map(fn ($id) => (int) $id)
                    ->sort()
                    ->values()
                    ->toArray();

                $correctOptionIds = $question->options
                    ->where('is_correct', true)
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->sort()
                    ->values()
                    ->toArray();

                if ($question->question_type === 'single_choice') {
                    if (
                        count($userOptionIds) === 1 &&
                        count($correctOptionIds) === 1 &&
                        $userOptionIds[0] === $correctOptionIds[0]
                    ) {
                        $isCorrect = true;
                    }
                } elseif ($question->question_type === 'multiple_choice') {
                    if ($userOptionIds === $correctOptionIds) {
                        $isCorrect = true;
                    }
                }
            }

            if ($isCorrect) {
                $correctAnswers++;
            }
        }

        $score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

        return [
            'score' => $score,
            'correctAnswers' => $correctAnswers,
            'totalQuestions' => $totalQuestions,
        ];
    }

    public function grantExtraAttempts(Request $request, Test $test, User $user)
    {
        // Проверяем, что пользователь имеет право (учитель/админ)
        if (! Auth::user()->can('edit courses')) {
            abort(403, 'Unauthorized');
        }

        // Валидируем данные
        $validated = $request->validate([
            'extra_attempts' => 'required|integer|min:1|max:100',
        ]);

        // Ищем существующую запись
        $extraAttempts = UserTestExtraAttempt::where('user_id', $user->id)
            ->where('test_id', $test->id)
            ->first();

        if ($extraAttempts) {
            // Увеличиваем количество дополнительных попыток
            $extraAttempts->extra_attempts += $validated['extra_attempts'];
            $extraAttempts->save();
        } else {
            // Создаём новую запись
            UserTestExtraAttempt::create([
                'user_id' => $user->id,
                'test_id' => $test->id,
                'extra_attempts' => $validated['extra_attempts'],
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->back()->with('success', "Студенту {$user->name} добавлено {$validated['extra_attempts']} попыток на прохождение теста.");
    }

    /**
     * Страница для начала прохождения теста (одностраничный режим).
     */
    public function attempt(Test $test)
    {
        abort_if(! $test->isAvailable(), 404);

        $user = Auth::user();

        // Проверка количества попыток
        if ($test->max_attempts > 0) {
            $completedAttempts = $test->attempts()
                ->where('user_id', $user->id)
                ->whereNotNull('ended_at')
                ->count();

            // Получаем дополнительные попытки для пользователя
            $extraAttempts = UserTestExtraAttempt::where('user_id', $user->id)
                ->where('test_id', $test->id)
                ->first();

            $maxAllowed = $test->max_attempts + ($extraAttempts ? $extraAttempts->extra_attempts : 0);

            if ($completedAttempts >= $maxAllowed) {
                return redirect()->back()->with('error', 'Вы исчерпали все попытки для этого теста.');
            }
        }

        // Проверяем, есть ли активная попытка (не завершённая)
        $activeAttempt = $test->attempts()
            ->where('user_id', $user->id)
            ->whereNull('ended_at')
            ->first();

        // Если нет активной попытки, создаём новую
        if (! $activeAttempt) {
            $lastAttemptNumber = TestAttempt::where('test_id', $test->id)
                ->where('user_id', $user->id)
                ->max('attempt_number') ?? 0;

            $activeAttempt = $test->attempts()->create([
                'user_id' => $user->id,
                'score' => 0,
                'attempt_number' => $lastAttemptNumber + 1,
                'started_at' => now(),
            ]);
        } elseif (! $activeAttempt->started_at) {
            // Ensure started_at is set if it was null
            $activeAttempt->update(['started_at' => now()]);
        }

        // Загружаем вопросы с вариантами
        $test->load(['questions' => function ($query) {
            $query->with('options');
        }]);

        // Формируем и фиксируем порядок вопросов в сессии (с учётом опции рандомизации),
        // привязывая его к конкретной попытке
        $sessionKey = "test_{$test->id}_attempt_{$activeAttempt->id}_question_order";
        $questionOrder = session($sessionKey);

        $questions = $test->questions;

        if (! $questionOrder) {
            $questionOrder = $questions->pluck('id')->toArray();

            // В режиме разбиения по страницам порядок задаётся вручную, не перемешиваем
            if ($test->randomize_questions && $test->display_mode !== 'paged') {
                shuffle($questionOrder);
            }

            session([$sessionKey => $questionOrder]);
        }

        // Применяем порядок к коллекции вопросов
        $questionsById = $questions->keyBy('id');
        $orderedQuestions = collect();
        foreach ($questionOrder as $qid) {
            if (isset($questionsById[$qid])) {
                $orderedQuestions->push($questionsById[$qid]);
            }
        }
        $test->setRelation('questions', $orderedQuestions);

        // Загружаем сохраненные ответы только для текущей активной попытки и только активные
        $tempAnswers = TemporaryAnswer::where('test_attempt_id', $activeAttempt->id)
            ->where('is_active', true)
            ->get();

        // Загружаем список типов вопросов для быстрого доступа
        $questionTypes = [];
        foreach ($test->questions as $q) {
            $questionTypes[$q->id] = $q->question_type;
        }

        $savedAnswers = [];
        foreach ($tempAnswers as $answer) {
            $questionType = $questionTypes[$answer->question_id] ?? null;

            // Для текстовых и развёрнутых ответов сохраняем как строка
            if (in_array($questionType, ['short_answer', 'rich_text_answer'])) {
                $savedAnswers[$answer->question_id] = $answer->answer_text;
            } elseif ($questionType === 'fill_in_dropdown') {
                // Для fill_in_dropdown сохраняем как массив: blank_id => option_id
                if (! isset($savedAnswers[$answer->question_id])) {
                    $savedAnswers[$answer->question_id] = [];
                }
                // answer_text содержит blank_id
                if ($answer->answer_text && $answer->option_id) {
                    $savedAnswers[$answer->question_id][$answer->answer_text] = $answer->option_id;
                }
            } else {
                // Для множественного выбора сохраняем как массив option_id
                if (! isset($savedAnswers[$answer->question_id])) {
                    $savedAnswers[$answer->question_id] = [];
                }
                if ($answer->option_id) {
                    $savedAnswers[$answer->question_id][] = $answer->option_id;
                }
            }
        }

        return view('layout', [
            'content' => view('test_attempt', [
                'test' => $test,
                'savedAnswers' => $savedAnswers,
                'attempt' => $activeAttempt,
            ]),
        ]);
    }

    /**
     * Прохождение теста по страницам/вопросам.
     */
    public function attemptPage(Test $test, $questionIndex = 1)
    {
        abort_if(! $test->isAvailable(), 404);

        $user = Auth::user();

        // Проверка количества попыток
        if ($test->max_attempts > 0) {
            $completedAttempts = $test->attempts()
                ->where('user_id', $user->id)
                ->whereNotNull('ended_at')
                ->count();

            // Получаем дополнительные попытки для пользователя
            $extraAttempts = UserTestExtraAttempt::where('user_id', $user->id)
                ->where('test_id', $test->id)
                ->first();

            $maxAllowed = $test->max_attempts + ($extraAttempts ? $extraAttempts->extra_attempts : 0);

            if ($completedAttempts >= $maxAllowed) {
                return redirect()->back()->with('error', 'Вы исчерпали все попытки для этого теста.');
            }
        }

        // Получаем текущую активную попытку
        $activeAttempt = $test->attempts()
            ->where('user_id', $user->id)
            ->whereNull('ended_at')
            ->first();

        // Если активной попытки нет, создаём новую (как в одностраничном режиме)
        if (! $activeAttempt) {
            $lastAttemptNumber = TestAttempt::where('test_id', $test->id)
                ->where('user_id', $user->id)
                ->max('attempt_number') ?? 0;

            $activeAttempt = $test->attempts()->create([
                'user_id' => $user->id,
                'score' => 0,
                'attempt_number' => $lastAttemptNumber + 1,
                'started_at' => now(),
            ]);
        } elseif (! $activeAttempt->started_at) {
            // Если попытка была создана без started_at — установим его
            $activeAttempt->update(['started_at' => now()]);
        }

        // Загружаем вопросы с опциями
        $test->load(['questions.options']);

        // Формируем и фиксируем порядок вопросов в сессии (с учётом опции рандомизации),
        // привязывая его к конкретной попытке
        $sessionKey = "test_{$test->id}_attempt_{$activeAttempt->id}_question_order";
        $questionOrder = session($sessionKey);

        $questions = $test->questions;

        if (! $questionOrder) {
            $questionOrder = $questions->pluck('id')->toArray();

            // В режиме разбиения по страницам порядок задаётся вручную, не перемешиваем
            if ($test->randomize_questions && $test->display_mode !== 'paged') {
                shuffle($questionOrder);
            }

            session([$sessionKey => $questionOrder]);
        }

        // Применяем порядок к коллекции вопросов
        $questionsById = $questions->keyBy('id');
        $orderedQuestions = collect();
        foreach ($questionOrder as $qid) {
            if (isset($questionsById[$qid])) {
                $orderedQuestions->push($questionsById[$qid]);
            }
        }
        $questions = $orderedQuestions;

        // Загружаем сохраненные ответы только для текущей активной попытки и только активные
        $tempAnswers = TemporaryAnswer::where('test_attempt_id', $activeAttempt->id)
            ->where('is_active', true)
            ->get();

        // Загружаем список типов вопросов для быстрого доступа
        $questionTypes = [];
        foreach ($test->questions as $q) {
            $questionTypes[$q->id] = $q->question_type;
        }

        $savedAnswers = [];
        foreach ($tempAnswers as $answer) {
            $questionType = $questionTypes[$answer->question_id] ?? null;

            // Для текстовых и развёрнутых ответов сохраняем как строка
            if (in_array($questionType, ['short_answer', 'rich_text_answer'])) {
                $savedAnswers[$answer->question_id] = $answer->answer_text;
            } elseif ($questionType === 'fill_in_dropdown') {
                // Для fill_in_dropdown сохраняем как массив: blank_id => option_id
                if (! isset($savedAnswers[$answer->question_id])) {
                    $savedAnswers[$answer->question_id] = [];
                }
                // answer_text содержит blank_id
                if ($answer->answer_text && $answer->option_id) {
                    $savedAnswers[$answer->question_id][$answer->answer_text] = $answer->option_id;
                }
            } else {
                // Для множественного выбора сохраняем как массив option_id
                if (! isset($savedAnswers[$answer->question_id])) {
                    $savedAnswers[$answer->question_id] = [];
                }
                if ($answer->option_id) {
                    $savedAnswers[$answer->question_id][] = $answer->option_id;
                }
            }
        }

        // Режим вывода: по одному вопросу или по страницам
        if ($test->display_mode === 'paged') {
            // Группируем вопросы по номеру страницы (из pivot)
            $pages = $questions->groupBy(function ($q) {
                return (int) ($q->pivot->page_number ?? 1);
            });

            // Список реальных номеров страниц в отсортированном виде
            $pageNumbers = $pages->keys()->sort()->values();
            $totalPages = $pageNumbers->count();

            if ($totalPages === 0) {
                return redirect()->route('tests.view', $test)
                    ->with('error', 'В тесте нет вопросов.');
            }

            // questionIndex здесь — индекс страницы (1..N)
            if ($questionIndex < 1) {
                $questionIndex = 1;
            }
            if ($questionIndex > $totalPages) {
                $questionIndex = $totalPages;
            }

            $currentPageNumber = $pageNumbers[$questionIndex - 1];
            $pageQuestions = $pages[$currentPageNumber];

            // Нумерация вопросов сквозная по всему тесту
            $globalIndexMap = [];
            $i = 1;
            foreach ($questions as $q) {
                $globalIndexMap[$q->id] = $i++;
            }

            return view('layout', [
                'content' => view('test_attempt_page_group', [
                    'test' => $test,
                    'questions' => $pageQuestions,
                    'pageIndex' => $questionIndex,
                    'totalPages' => $totalPages,
                    'savedAnswers' => $savedAnswers,
                    'globalIndexMap' => $globalIndexMap,
                ]),
            ]);
        }

        // Обычный режим "по одному вопросу"
        if ($questionIndex < 1) {
            $questionIndex = 1;
        }
        if ($questionIndex > $questions->count()) {
            $questionIndex = $questions->count();
        }

        $question = $questions[$questionIndex - 1];

        return view('layout', [
            'content' => view('test_attempt_page', [
                'test' => $test,
                'question' => $question,
                'questionIndex' => $questionIndex,
                'totalQuestions' => $questions->count(),
                'savedAnswers' => $savedAnswers,
            ]),
        ]);
    }

    /**
     * Обработка сохранения временного ответа (AJAX).
     */
    public function saveAnswer(Request $request, Test $test)
    {
        // Для fill_in_dropdown
        if ($request->has('fill_in_dropdown_answers')) {
            $questionId = $request->input('question_id');
            $answersArr = $request->input('fill_in_dropdown_answers'); // [blank_id => option_id]
            // Сохраняем в сессии
            $answers = session("test_{$test->id}_answers", []);
            $answers[$questionId] = $answersArr;
            session(["test_{$test->id}_answers" => $answers]);

            if (Auth::check()) {
                $userId = Auth::id();
                $attempt = TestAttempt::where('test_id', $test->id)
                    ->where('user_id', $userId)
                    ->whereNull('ended_at')
                    ->first();
                if (! $attempt) {
                    return response()->json(['error' => 'Test not started'], 403);
                }
                if ($test->time_limit && $attempt->started_at) {
                    $elapsed = now()->diffInSeconds($attempt->started_at);
                    $timeLimitSeconds = $test->time_limit * 60;
                    if ($elapsed > $timeLimitSeconds) {
                        return response()->json(['error' => 'Time is up. Answer not saved.'], 403);
                    }
                }
                // Удаляем предыдущие ответы на этот вопрос
                TemporaryAnswer::where('user_id', $userId)
                    ->where('test_id', $test->id)
                    ->where('question_id', $questionId)
                    ->delete();
                // Сохраняем каждый пропуск как отдельный TemporaryAnswer
                foreach ($answersArr as $blankId => $optionId) {
                    TemporaryAnswer::create([
                        'user_id' => $userId,
                        'test_id' => $test->id,
                        'test_attempt_id' => $attempt->id,
                        'question_id' => $questionId,
                        'option_id' => $optionId,
                        'answer_text' => $blankId, // сохраняем blank_id для удобства
                    ]);
                }
            }

            return response()->json(['success' => true]);
        }

        $questionId = $request->input('question_id');
        $optionIds = $request->input('option_id');
        $answerText = $request->input('answer_text');
        $richTextAnswer = $request->input('rich_text_answer');

        // Для текстовых ответов
        if ($request->has('answer_text')) {
            // Сохраняем в сессии
            $answers = session("test_{$test->id}_answers", []);
            $answers[$questionId] = $answerText;
            session(["test_{$test->id}_answers" => $answers]);

            if (Auth::check()) {
                $userId = Auth::id();

                // Получаем текущую попытку
                $attempt = TestAttempt::where('test_id', $test->id)
                    ->where('user_id', $userId)
                    ->whereNull('ended_at')
                    ->first();

                if (! $attempt) {
                    return response()->json(['error' => 'Test not started'], 403);
                }

                // Проверка лимита времени (только если time_limit установлен)
                if ($test->time_limit && $attempt->started_at) {
                    $elapsed = now()->diffInSeconds($attempt->started_at);
                    $timeLimitSeconds = $test->time_limit * 60;

                    if ($elapsed > $timeLimitSeconds) {
                        return response()->json(['error' => 'Time is up. Answer not saved.'], 403);
                    }
                }

                // Удаляем предыдущие ответы на этот вопрос
                TemporaryAnswer::where('user_id', $userId)
                    ->where('test_id', $test->id)
                    ->where('question_id', $questionId)
                    ->delete();

                // Вставляем новый текстовый ответ
                TemporaryAnswer::create([
                    'user_id' => $userId,
                    'test_id' => $test->id,
                    'test_attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                    'option_id' => null,
                    'answer_text' => $answerText,
                ]);
            }

            return response()->json(['success' => true]);
        }

        // Для развёрнутых ответов
        if ($request->has('rich_text_answer')) {
            // Сохраняем в сессии
            $answers = session("test_{$test->id}_answers", []);
            $answers[$questionId] = $richTextAnswer;
            session(["test_{$test->id}_answers" => $answers]);

            if (Auth::check()) {
                $userId = Auth::id();

                // Получаем текущую попытку
                $attempt = TestAttempt::where('test_id', $test->id)
                    ->where('user_id', $userId)
                    ->whereNull('ended_at')
                    ->first();

                if (! $attempt) {
                    return response()->json(['error' => 'Test not started'], 403);
                }

                // Проверка лимита времени (только если time_limit установлен)
                if ($test->time_limit && $attempt->started_at) {
                    $elapsed = now()->diffInSeconds($attempt->started_at);
                    $timeLimitSeconds = $test->time_limit * 60;

                    if ($elapsed > $timeLimitSeconds) {
                        return response()->json(['error' => 'Time is up. Answer not saved.'], 403);
                    }
                }

                // Удаляем предыдущие ответы на этот вопрос
                TemporaryAnswer::where('user_id', $userId)
                    ->where('test_id', $test->id)
                    ->where('question_id', $questionId)
                    ->delete();

                // Вставляем новый развёрнутый ответ
                TemporaryAnswer::create([
                    'user_id' => $userId,
                    'test_id' => $test->id,
                    'test_attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                    'option_id' => null,
                    'answer_text' => $richTextAnswer,
                ]);
            }

            return response()->json(['success' => true]);
        }

        // Для множественного выбора
        // Убедимся, что $optionIds всегда массив
        if (! $request->has('answer_text') && $optionIds) {
            $optionIds = (array) $optionIds;

            // Сохраняем в сессии
            $answers = session("test_{$test->id}_answers", []);
            $answers[$questionId] = $optionIds;
            session(["test_{$test->id}_answers" => $answers]);

            if (Auth::check()) {
                $userId = Auth::id();

                // Получаем текущую попытку
                $attempt = TestAttempt::where('test_id', $test->id)
                    ->where('user_id', $userId)
                    ->whereNull('ended_at')
                    ->first();

                if (! $attempt) {
                    return response()->json(['error' => 'Test not started'], 403);
                }

                // Проверка лимита времени (только если time_limit установлен)
                if ($test->time_limit && $attempt->started_at) {
                    $elapsed = now()->diffInSeconds($attempt->started_at);
                    $timeLimitSeconds = $test->time_limit * 60;

                    if ($elapsed > $timeLimitSeconds) {
                        return response()->json(['error' => 'Time is up. Answer not saved.'], 403);
                    }
                }

                // Удаляем предыдущие ответы на этот вопрос
                TemporaryAnswer::where('user_id', $userId)
                    ->where('test_id', $test->id)
                    ->where('question_id', $questionId)
                    ->delete();

                // Вставляем новый(е) ответ(ы)
                foreach ($optionIds as $optionId) {
                    TemporaryAnswer::create([
                        'user_id' => $userId,
                        'test_id' => $test->id,
                        'test_attempt_id' => $attempt->id,
                        'question_id' => $questionId,
                        'option_id' => $optionId,
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }
}
