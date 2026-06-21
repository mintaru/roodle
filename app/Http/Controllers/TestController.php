<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSectionItem;
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
            'is_details_available' => 'nullable|boolean',
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
        $validatedData['is_details_available'] = $request->has('is_details_available');
        $validatedData['display_mode'] = $request->input('display_mode', 'single_page');

        // Опция: добавить в общий банк
        $addToBank = $request->has('add_to_bank');

        if ($addToBank) {
            // Общий банк — тест не привязан к конкретному курсу и не имеет владельца
            $validatedData['is_global'] = true;
            $validatedData['user_id'] = null;
            $validatedData['course_id'] = null;
            $test = Test::create($validatedData);
        } else {
            // Привязка к пользователю (частный тест) — сохраняем как собственность пользователя
            $validatedData['is_global'] = false;
            $validatedData['user_id'] = $request->user()->id;
            $validatedData['course_id'] = null;
            $test = Test::create($validatedData);
        }

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
        if (! (Auth::check() && Auth::user()->hasAnyRole(['admin', 'teacher']))) {
            abort_if(! $test->isAvailable(), 404);
        }

        $test->load('questions.options');

        // Выбираем все вопросы из банка
        $allQuestions = \App\Models\Question::with('options')->get();

        return view('test_show', [
            'test' => $test,
            'allQuestions' => $allQuestions,
        ]);
    }

    public function view(Test $test)
    {
        if (! (Auth::check() && Auth::user()->hasAnyRole(['admin', 'teacher']))) {
            abort_if(! $test->isAvailable(), 404);
        }

        $user = Auth::user();

        // Resolve course through direct relation or section items
        $course = $test->course;
        if (! $course) {
            $sectionItem = CourseSectionItem::where('item_type', Test::class)
                ->where('item_id', $test->id)
                ->with('section.course')
                ->first();
            $course = $sectionItem?->section?->course;
        }

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

            // Если время на попытку истекло — завершаем её автоматически
            if ($test->time_limit > 0 && $activeAttempt->started_at) {
                $deadline = $activeAttempt->started_at->timestamp + ($test->time_limit * 60);
                if (time() > $deadline) {
                    DB::transaction(function () use ($activeAttempt, $test) {
                        $scoreData = $this->calculateScoreForAttempt($test, $activeAttempt);
                        $activeAttempt->update([
                            'ended_at' => now(),
                            'score' => round($scoreData['score']),
                        ]);
                        TemporaryAnswer::where('test_attempt_id', $activeAttempt->id)
                            ->update(['is_active' => false]);
                    });

                    $hasActiveAttempt = false;
                    $activeAttempt = null;

                    // Пересчитываем количество завершённых попыток
                    $userAttemptsCount = $test->attempts()
                        ->where('user_id', $user->id)
                        ->whereNotNull('ended_at')
                        ->count();
                    $userAttempts = $test->attempts()
                        ->where('user_id', $user->id)
                        ->whereNotNull('ended_at')
                        ->get();
                    $remaining = $isUnlimited
                        ? '∞'
                        : max(0, $maxAttemptsForUser - $userAttemptsCount);
                }
            }
        }

        return view('tests.view', compact(
            'test',
            'course',
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
            'question_text' => 'nullable|required_unless:question_type,fill_in_dropdown|string',
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
            'fill_text' => 'nullable|required_if:question_type,fill_in_dropdown|string',
            'dropdown_options' => 'nullable|required_if:question_type,fill_in_dropdown|array',
            'dropdown_options.*' => 'nullable|array',
            'dropdown_correct' => 'nullable|required_if:question_type,fill_in_dropdown|array',
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

    public function saveProgress(Request $request, Test $test)
    {
        $attempt = TestAttempt::where('test_id', $test->id)
            ->where('user_id', $request->user()->id)
            ->whereNull('ended_at')   // ← было finished_at
            ->latest()
            ->first();

        if ($attempt) {
            $attempt->update(['last_question_index' => $request->input('question_index')]);
        }

        return response()->json(['ok' => true]);
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
        $user = Auth::user();

        // Сначала ищем активную попытку
        $attempt = $test->attempts()
            ->where('user_id', $user->id)
            ->whereNull('ended_at')
            ->first();

        // Если нет активной — берём последнюю завершённую (например, завершена по таймеру)
        if (! $attempt) {
            $attempt = $test->attempts()
                ->where('user_id', $user->id)
                ->whereNotNull('ended_at')
                ->latest('ended_at')
                ->first();
        }

        if (! $attempt) {
            return redirect()->route('tests.view', $test)
                ->with('error', 'Активная попытка не найдена.');
        }

        // Если попытка уже завершена — просто показываем результат без пересчёта
        if ($attempt->ended_at) {
            return redirect()->route('test-attempts.details', $attempt);
        }

        // Иначе — завершаем попытку
        $scoreData = $this->calculateScoreForAttempt($test, $attempt);
        $score = $scoreData['score'];

        DB::transaction(function () use ($attempt, $score) {
            $attempt->update([
                'score' => round($score),
                'ended_at' => now(),
            ]);
            TemporaryAnswer::where('test_attempt_id', $attempt->id)
                ->update(['is_active' => false]);
        });

        return redirect()->route('test-attempts.details', $attempt);
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

        // Берём started_at из активной попытки, а не из сессии (сессия не писалась)
        $attempt = $test->attempts()
            ->where('user_id', $user->id)
            ->whereNull('ended_at')
            ->first();

        if (!$attempt || !$attempt->started_at) {
            return response()->json(['time_left' => $test->time_limit * 60], 400);
        }

        $now = now();
        $elapsedSeconds = $now->diffInSeconds($attempt->started_at);
        $timeLimitSeconds = $test->time_limit * 60;
        $timeLeft = max(0, $timeLimitSeconds - $elapsedSeconds);

        return response()->json([
            'time_left' => $timeLeft,
            'server_time' => $now->timestamp,  // <- маленькими, как в page_group
        ]);
    }

    /**
     * Отображает результаты теста - кто из учеников сейчас проходит, их статусы и результаты
     */
    public function results(Test $test)
    {
        $user = Auth::user();

        $course = $test->course;
        if (! $course) {
            $sectionItem = \App\Models\CourseSectionItem::where('item_type', Test::class)
                ->where('item_id', $test->id)
                ->with('section.course')
                ->first();
            $course = $sectionItem?->section?->course;
        }

        // If teacher/admin — show results for course users or for users who attempted the test when no course
        if ($user && $user->can('edit courses')) {
            $groups = $course ? $course->groups()->get() : collect();
            $courseGroupIds = $groups->pluck('id');

            if ($course) {
                $usersInCourse = $course->groups()
                    ->with('users')
                    ->get()
                    ->pluck('users')
                    ->flatten()
                    ->unique('id')
                    ->values();
            } else {
                $userIds = $test->attempts()->pluck('user_id')->unique()->filter()->values()->toArray();
                $usersInCourse = \App\Models\User::whereIn('id', $userIds)->get();
            }

            $studentsData = [];
            foreach ($usersInCourse as $user) {
                // Все попытки пользователя (завершённые и текущие)
                $attempts = $test->attempts()
                    ->where('user_id', $user->id)
                    ->orderBy('attempt_number', 'asc')
                    ->get();

                $activeAttempt = $attempts->where('ended_at', null)->first();
                $completedAttempts = $attempts->where('ended_at', '!=', null);
                $lastCompletedAttempt = $completedAttempts->last();

                // Какой номер попытки сейчас/будет
                $currentAttemptNumber = $attempts->count() + 1;

                // Статус
                $status = 'не начинали';
                $timeSpent = null;
                $currentQuestion = null;
                $totalQuestions = $test->questions()->count();

                if ($activeAttempt) {
                    $status = 'в процессе';
                    // Считаем секунды, прошедшие с начала попытки
                    if ($activeAttempt->started_at) {
                        $timeSpent = now()->diffInSeconds($activeAttempt->started_at);
                    }

                    // Определяем текущий вопрос по last_question_index (сохраняется при навигации)
                    $currentQuestion = $activeAttempt->last_question_index !== null
                        ? $activeAttempt->last_question_index + 1
                        : 1;
                    if ($currentQuestion > $totalQuestions) {
                        $currentQuestion = $totalQuestions;
                    }
                } elseif ($completedAttempts->count() > 0) {
                    $status = 'завершили';
                    if ($lastCompletedAttempt && $lastCompletedAttempt->started_at && $lastCompletedAttempt->ended_at) {
                        $timeSpent = $lastCompletedAttempt->started_at->diffInSeconds($lastCompletedAttempt->ended_at);
                    }
                }

                $studentsData[] = [
                    'user' => $user,
                    'status' => $status,
                    'attempts' => $attempts,
                    'activeAttempt' => $activeAttempt,
                    'completedAttempts' => $completedAttempts,
                    'lastCompletedAttempt' => $lastCompletedAttempt,
                    'timeSpent' => $timeSpent,
                    'current_question' => $currentQuestion,
                    'totalQuestions' => $totalQuestions,
                    'current_attempt_number' => $currentAttemptNumber,
                    'group_ids' => $courseGroupIds->isNotEmpty()
                        ? $user->groups()->whereIn('groups.id', $courseGroupIds)->pluck('groups.id')->toArray()
                        : [],
                ];
            }

            return view('tests.results', compact('test', 'studentsData', 'course', 'groups'));
        }

        // Non-teacher: allow students to view only their own results
        if (! $user) {
            abort(403, 'Unauthorized');
        }

        $groups = $course ? $course->groups()->get() : collect();

        $attempts = $test->attempts()
            ->where('user_id', $user->id)
            ->orderBy('attempt_number', 'asc')
            ->get();

        $activeAttempt = $attempts->where('ended_at', null)->first();
        $completedAttempts = $attempts->where('ended_at', '!=', null);
        $lastCompletedAttempt = $completedAttempts->last();
        $currentAttemptNumber = $attempts->count() + 1;

        $status = 'не начинали';
        $timeSpent = null;
        $current_question = null;
        $totalQuestions = $test->questions()->count();

        if ($activeAttempt) {
            $status = 'в процессе';
            if ($activeAttempt->started_at) {
                $timeSpent = now()->diffInSeconds($activeAttempt->started_at);
            }
            $current_question = $activeAttempt->last_question_index !== null
                ? $activeAttempt->last_question_index + 1
                : 1;
            if ($current_question > $totalQuestions) {
                $current_question = $totalQuestions;
            }
        } elseif ($completedAttempts->count() > 0) {
            $status = 'завершили';
            if ($lastCompletedAttempt && $lastCompletedAttempt->started_at && $lastCompletedAttempt->ended_at) {
                $timeSpent = $lastCompletedAttempt->started_at->diffInSeconds($lastCompletedAttempt->ended_at);
            }
        }

        $studentsData = [[
            'user' => $user,
            'status' => $status,
            'attempts' => $attempts,
            'activeAttempt' => $activeAttempt,
            'completedAttempts' => $completedAttempts,
            'lastCompletedAttempt' => $lastCompletedAttempt,
            'timeSpent' => $timeSpent,
            'current_question' => $current_question,
            'totalQuestions' => $totalQuestions,
            'current_attempt_number' => $currentAttemptNumber,
            'group_ids' => [],
        ]];

        return view('tests.results', compact('test', 'studentsData', 'course', 'groups'));
    }

    /**
     * Просмотр деталей попытки ученика - его ответы
     */
    public function viewAttemptDetails(TestAttempt $attempt)
    {
        // Не показываем детали незавершённой попытки
        if (! $attempt->ended_at) {
            abort(403, 'Попытка ещё не завершена');
        }

        $test = $attempt->test;
        $user = $attempt->user;

        // Resolve course through direct relation or section items (same as view())
        $course = $test->course;
        if (! $course) {
            $sectionItem = CourseSectionItem::where('item_type', Test::class)
                ->where('item_id', $test->id)
                ->with('section.course')
                ->first();
            $course = $sectionItem?->section?->course;
        }

        $authUser = Auth::user();

        // Allow owner of the attempt or users with course-edit rights (teachers/admins)
        if (! ($authUser && ($authUser->id === $user->id || $authUser->can('edit courses')))) {
            abort(403, 'Unauthorized');
        }

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
        if (! (Auth::check() && Auth::user()->hasAnyRole(['admin', 'teacher']))) {
            abort_if(! $test->isAvailable(), 404);
        }

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

        error_log('[ATTEMPT_DEBUG] ===== attempt() called for test_id='.$test->id);
        error_log('[ATTEMPT_DEBUG] Active attempt ID: '.$activeAttempt->id);
        error_log('[ATTEMPT_DEBUG] Active attempt started_at: '.($activeAttempt->started_at ? $activeAttempt->started_at->toDateTimeString() : 'NULL'));
        error_log('[ATTEMPT_DEBUG] Active attempt ended_at: '.($activeAttempt->ended_at ? $activeAttempt->ended_at->toDateTimeString() : 'NULL'));
        error_log('[ATTEMPT_DEBUG] Test time_limit: '.$test->time_limit);
        error_log('[ATTEMPT_DEBUG] Test time_limit (int): '.(int)$test->time_limit);

        $timeLimitExceeded = false;
        if ($test->time_limit > 0 && $activeAttempt->started_at) {
            $elapsedSeconds = now()->diffInSeconds($activeAttempt->started_at);
            error_log('[ATTEMPT_DEBUG] Time limit check: elapsedSeconds='.$elapsedSeconds.', limit='.($test->time_limit * 60));
            if ($elapsedSeconds > ($test->time_limit * 60)) {
                $timeLimitExceeded = true;
                error_log('[ATTEMPT_DEBUG] TIME LIMIT EXCEEDED! Setting ended_at...');
                // Mark attempt as ended if time limit passed
                if (is_null($activeAttempt->ended_at)) {
                    $activeAttempt->update(['ended_at' => now()]);
                    error_log('[ATTEMPT_DEBUG] ended_at set, redirecting to result...');

                    // Redirect to attempt details if test ended by time limit
                    return redirect()->route('test-attempts.details', $activeAttempt);
                }
            }
        }

        // Если попытка завершена (например, если timeLimitExceeded = true выше), то перенаправляем на страницу деталей попытки
        if ($activeAttempt->ended_at) {
            error_log('[ATTEMPT_DEBUG] Attempt already has ended_at, redirecting to attempt details...');
            return redirect()->route('test-attempts.details', $activeAttempt);
        }

        $initialTimeRemaining = $test->time_limit * 60; // в секундах
        if ($test->time_limit > 0 && $activeAttempt->started_at) {
            $elapsed = now()->diffInSeconds($activeAttempt->started_at);
            $initialTimeRemaining = max(0, ($test->time_limit * 60) - $elapsed);
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

        $initialAnsweredCount = count($savedAnswers);

        $answeredQuestionIds = array_keys($savedAnswers);
        $answeredIndices = [];
        foreach ($questionOrder as $idx => $qid) {
            if (in_array($qid, $answeredQuestionIds)) {
                $answeredIndices[] = $idx;
            }
        }

        return view('test_attempt', [
            'test' => $test,
            'savedAnswers' => $savedAnswers,
            'activeAttempt' => $activeAttempt,
            'lastQuestionIndex' => $activeAttempt->last_question_index ?? 0,
            'timeLimitExceeded' => $timeLimitExceeded,
            'serverTime' => now()->timestamp,
            'totalQuestions' => $test->questions->count(),
            'initialAnsweredCount' => $initialAnsweredCount,
            'answeredIndices' => $answeredIndices,
            'initialTimeRemaining' => $initialTimeRemaining,
        ]);


    }

    /**
     * Закрывает просроченную попытку (по таймеру) и возвращает на страницу теста.
     */
    public function closeExpiredAttempt(Test $test)
    {
        if (! (Auth::check() && Auth::user()->hasAnyRole(['admin', 'teacher']))) {
            abort_if(! $test->isAvailable(), 404);
        }

        $user = Auth::user();

        $activeAttempt = $test->attempts()
            ->where('user_id', $user->id)
            ->whereNull('ended_at')
            ->first();

        if ($activeAttempt) {
            DB::transaction(function () use ($activeAttempt, $test) {
                $scoreData = $this->calculateScoreForAttempt($test, $activeAttempt);
                $activeAttempt->update([
                    'ended_at' => now(),
                    'score' => round($scoreData['score']),
                ]);
                TemporaryAnswer::where('test_attempt_id', $activeAttempt->id)
                    ->update(['is_active' => false]);
            });
        }

        return redirect()->route('tests.view', $test);
    }

    /**
     * Принудительно завершает активную попытку и перенаправляет на создание новой.
     */
    public function forceNewAttempt(Test $test)
    {
        if (! (Auth::check() && Auth::user()->hasAnyRole(['admin', 'teacher']))) {
            abort_if(! $test->isAvailable(), 404);
        }

        $user = Auth::user();

        $activeAttempt = $test->attempts()
            ->where('user_id', $user->id)
            ->whereNull('ended_at')
            ->first();

        if ($activeAttempt) {
            $activeAttempt->update(['ended_at' => now()]);
            TemporaryAnswer::where('test_attempt_id', $activeAttempt->id)
                ->update(['is_active' => false]);
        }

        if ($test->display_mode === 'single_page') {
            return redirect()->route('tests.attempt', $test);
        }

        return redirect()->route('tests.attempt.page', [$test->id, 1]);
    }

    /**
     * Загрузка одного вопроса через AJAX (stream-режим).
     */
    public function getQuestion(Test $test, $questionIndex)
    {
        $user = Auth::user();

        $activeAttempt = $test->attempts()
            ->where('user_id', $user->id)
            ->whereNull('ended_at')
            ->first();

        if (! $activeAttempt) {
            return response()->json(['error' => 'No active attempt'], 403);
        }

        if ($test->time_limit > 0 && $activeAttempt->started_at) {
            $elapsedSeconds = now()->diffInSeconds($activeAttempt->started_at);
            if ($elapsedSeconds > ($test->time_limit * 60)) {
                return response()->json(['error' => 'Time is up'], 403);
            }
        }

        $test->load(['questions.options']);

        $sessionKey = "test_{$test->id}_attempt_{$activeAttempt->id}_question_order";
        $questionOrder = session($sessionKey);

        $questions = $test->questions;

        if (! $questionOrder) {
            $questionOrder = $questions->pluck('id')->toArray();
            if ($test->randomize_questions) {
                shuffle($questionOrder);
            }
            session([$sessionKey => $questionOrder]);
        }

        $questionsById = $questions->keyBy('id');
        $orderedQuestions = collect();
        foreach ($questionOrder as $qid) {
            if (isset($questionsById[$qid])) {
                $orderedQuestions->push($questionsById[$qid]);
            }
        }

        $totalQuestions = $orderedQuestions->count();
        if ($questionIndex < 0 || $questionIndex >= $totalQuestions) {
            return response()->json(['error' => 'Invalid question index'], 400);
        }

        $question = $orderedQuestions[$questionIndex];

        $tempAnswers = TemporaryAnswer::where('test_attempt_id', $activeAttempt->id)
            ->where('is_active', true)
            ->get();

        $savedAnswers = [];
        foreach ($tempAnswers as $answer) {
            $questionType = $questionsById[$answer->question_id]->question_type ?? null;
            if (in_array($questionType, ['short_answer', 'rich_text_answer'])) {
                $savedAnswers[$answer->question_id] = $answer->answer_text;
            } elseif ($questionType === 'fill_in_dropdown') {
                if (! isset($savedAnswers[$answer->question_id])) {
                    $savedAnswers[$answer->question_id] = [];
                }
                if ($answer->answer_text && $answer->option_id) {
                    $savedAnswers[$answer->question_id][$answer->answer_text] = $answer->option_id;
                }
            } else {
                if (! isset($savedAnswers[$answer->question_id])) {
                    $savedAnswers[$answer->question_id] = [];
                }
                if ($answer->option_id) {
                    $savedAnswers[$answer->question_id][] = $answer->option_id;
                }
            }
        }

        $html = view('partials._question_card', [
            'question' => $question,
            'questionIndex' => $questionIndex,
            'savedAnswers' => $savedAnswers,
        ])->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Прохождение теста по страницам/вопросам.
     */
    public function attemptPage(Test $test, $questionIndex = 1)
    {
        if (! (Auth::check() && Auth::user()->hasAnyRole(['admin', 'teacher']))) {
            abort_if(! $test->isAvailable(), 404);
        }

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

            $initialTimeRemaining = $test->time_limit * 60; // в секундах
            if ($test->time_limit > 0 && $activeAttempt->started_at) {
                $elapsed = now()->diffInSeconds($activeAttempt->started_at);
                $initialTimeRemaining = max(0, ($test->time_limit * 60) - $elapsed);
            }

            return view('test_attempt_page_group', [
                'test' => $test,
                'questions' => $pageQuestions,
                'pageIndex' => $questionIndex,
                'totalPages' => $totalPages,
                'savedAnswers' => $savedAnswers,
                'globalIndexMap' => $globalIndexMap,
                'activeAttempt' => $activeAttempt,
                'serverTime' => now()->timestamp,
                'initialTimeRemaining' => $initialTimeRemaining,
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

    }

    /**
     * Обработка сохранения временного ответа (AJAX).
     */
    public function saveAnswer(Request $request, Test $test)
    {
        // Для fill_in_dropdown
        if ($request->has('fill_in_dropdown_answers')) {
            $questionId = $request->input('question_id');
            $answersArr = $request->input('fill_in_dropdown_answers');
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
                    if ($elapsed > $test->time_limit * 60) {
                        return response()->json(['error' => 'Time is up. Answer not saved.'], 403);
                    }
                }
                TemporaryAnswer::where('test_attempt_id', $attempt->id)
                    ->where('question_id', $questionId)
                    ->delete();
                foreach ($answersArr as $blankId => $optionId) {
                    TemporaryAnswer::create([
                        'user_id' => $userId,
                        'test_id' => $test->id,
                        'test_attempt_id' => $attempt->id,
                        'question_id' => $questionId,
                        'option_id' => $optionId,
                        'answer_text' => $blankId,
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
            $answers = session("test_{$test->id}_answers", []);
            $answers[$questionId] = $answerText;
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
                    if ($elapsed > $test->time_limit * 60) {
                        return response()->json(['error' => 'Time is up. Answer not saved.'], 403);
                    }
                }
                TemporaryAnswer::where('test_attempt_id', $attempt->id)
                    ->where('question_id', $questionId)
                    ->delete();
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
            $answers = session("test_{$test->id}_answers", []);
            $answers[$questionId] = $richTextAnswer;
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
                    if ($elapsed > $test->time_limit * 60) {
                        return response()->json(['error' => 'Time is up. Answer not saved.'], 403);
                    }
                }
                TemporaryAnswer::where('test_attempt_id', $attempt->id)
                    ->where('question_id', $questionId)
                    ->delete();
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
        if ($optionIds) {
            $optionIds = (array) $optionIds;
            $answers = session("test_{$test->id}_answers", []);
            $answers[$questionId] = $optionIds;
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
                    if ($elapsed > $test->time_limit * 60) {
                        return response()->json(['error' => 'Time is up. Answer not saved.'], 403);
                    }
                }
                TemporaryAnswer::where('test_attempt_id', $attempt->id)
                    ->where('question_id', $questionId)
                    ->delete();
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

    /**
     * Синхронизация таймера с сервером.
     * Возвращает количество прошедших секунд с начала попытки.
     */
    public function getElapsedTime(Test $test)
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $attempt = $test->attempts()
            ->where('user_id', Auth::id())
            ->whereNull('ended_at')
            ->first();

        if (! $attempt) {
            return response()->json(['error' => 'No active attempt'], 400);
        }

        // Рассчитываем прошедшие секунды с начала попытки
        $elapsedSeconds = $attempt->started_at
            ? now()->diffInSeconds($attempt->started_at)
            : 0;

        // Проверяем не превышен ли лимит времени
        $timeLimitExceeded = false;
        if ($test->time_limit > 0) {
            $timeLimitSeconds = $test->time_limit * 60;
            if ($elapsedSeconds >= $timeLimitSeconds) {
                $timeLimitExceeded = true;
            }
        }

        return response()->json([
            'elapsed_seconds' => $elapsedSeconds,
            'time_limit_exceeded' => $timeLimitExceeded,
            'server_time' => now()->timestamp * 1000,
        ]);
    }

    public function clearAnswer(Request $request, Test $test)
    {
        $questionId = $request->input('question_id');

        if (! $questionId) {
            return response()->json(['error' => 'Question ID is required'], 400);
        }

        // Удаляем из сессии
        $answers = session("test_{$test->id}_answers", []);
        unset($answers[$questionId]);
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

            // Проверка лимита времени
            if ($test->time_limit && $attempt->started_at) {
                $elapsed = now()->diffInSeconds($attempt->started_at);
                $timeLimitSeconds = $test->time_limit * 60;

                if ($elapsed > $timeLimitSeconds) {
                    return response()->json(['error' => 'Time is up. Answer not cleared.'], 403);
                }
            }

            // Удаляем ответ из БД
            TemporaryAnswer::where('test_attempt_id', $attempt->id)
                ->where('question_id', $questionId)
                ->delete();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Форма редактирования полей теста (не вопросов)
     */
    public function edit(Test $test)
    {
        // Проверяем права доступа
        if (! Auth::user()->can('edit courses')) {
            abort(403, 'Unauthorized');
        }

        $course = $test->course;

        return view('layout', [
            'content' => view('test_edit_form', [
                'test' => $test,
                'course' => $course,
            ]),
        ]);
    }

    /**
     * Обновление полей теста (не вопросов)
     */
    public function update(Request $request, Test $test)
    {
        // Проверяем права доступа
        if (! Auth::user()->can('edit courses')) {
            abort(403, 'Unauthorized');
        }

        // Валидируем данные
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_attempts' => 'nullable|integer|min:1',
            'unlimited_attempts' => 'nullable|boolean',
            'time_limit' => 'nullable|integer|min:0',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'randomize_questions' => 'nullable|boolean',
            'is_details_available' => 'nullable|boolean',
            'display_mode' => 'required|in:single_page,per_question,paged',
        ]);

        $validatedData['time_limit'] = $request->input('time_limit', 0);

        // Преобразуем период доступности в UTC
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
            $validatedData['max_attempts'] = $request->input('max_attempts', 1);
        }

        // Настройки отображения теста
        $validatedData['randomize_questions'] = $request->has('randomize_questions');
        $validatedData['is_details_available'] = $request->has('is_details_available');
        $validatedData['display_mode'] = $request->input('display_mode', 'single_page');

        // Опция: добавить в общий банк
        if ($request->has('add_to_bank')) {
            $validatedData['is_global'] = true;
            $validatedData['user_id'] = null;
            $validatedData['course_id'] = null;
        } else {
            $validatedData['is_global'] = false;
            $validatedData['user_id'] = $request->user()->id;
            $validatedData['course_id'] = null;
        }

        // Обновляем тест
        $test->update($validatedData);

        if ($test->course) {
            return redirect()->route('courses.show', $test->course)
                ->with('success', 'Параметры теста успешно обновлены!');
        }

        return redirect()->route('tests.show', $test)
            ->with('success', 'Параметры теста успешно обновлены!');
    }
}
