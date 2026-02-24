<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Question;
use App\Models\TemporaryAnswer;
use App\Models\TestAttempt;
use App\Models\Test;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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
            'period_end' => 'nullable|date|after:period_start'
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
        $user = Auth::user();

        // Количество попыток пользователя
        $userAttemptsCount = $test->attempts()->where('user_id', $user->id)->whereNotNull('ended_at')->count();
        $userAttempts = $test->attempts()->where('user_id', $user->id)->whereNotNull('ended_at')->get();

        // Оставшиеся попытки
        $remaining = $test->max_attempts == 0
            ? '∞'
            : max(0, $test->max_attempts - $userAttemptsCount);

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

        return view('tests.view', compact('test', 'userAttemptsCount', 'userAttempts', 'remaining', 'hasActiveAttempt', 'activeAttempt'));
    }

    /**
     * Сохраняет новый вопрос для теста.
     */
    public function storeQuestion(Request $request, Test $test)
    {
        // Валидация
        $validatedData = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:single_choice,multiple_choice,short_answer',
            'options' => 'required_if:question_type,single_choice,multiple_choice|array|min:2',
            'options.*' => 'required_if:question_type,single_choice,multiple_choice|string',
            'correct_option' => 'required_if:question_type,single_choice|integer',
            'correct_options' => 'required_if:question_type,multiple_choice|array',
            'correct_options.*' => 'integer',
            'correct_answers' => 'required_if:question_type,short_answer|array|min:1',
            'correct_answers.*' => 'required_if:question_type,short_answer|string|min:1',
            'case_insensitive' => 'nullable|in:0,1',
        ]);

        // Создаём вопрос
        $question = Question::create([
            'question_text' => $validatedData['question_text'],
            'question_type' => $validatedData['question_type'],
        ]);

        if ($validatedData['question_type'] === 'short_answer') {
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
        } else {
            // Сохраняем варианты и отмечаем правильные
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

    public function result(Test $test, Request $request)
    {
        $user = Auth::user();

        // Получаем активную попытку из БД
        $attempt = $test->attempts()
            ->where('user_id', $user->id)
            ->whereNull('ended_at')
            ->first();

        // Если активной попытки нет, перенаправляем
        if (!$attempt) {
            return redirect()->route('tests.view', $test)
                ->with('error', 'Активная попытка не найдена.');
        }

        // Берём ответы из POST и TemporaryAnswers
        $postAnswers = $request->input('answers', []);
        $textAnswers = $request->input('text_answers', []);

        // Нормализуем POST ответы
        $merged = [];
        foreach ($postAnswers as $qId => $val) {
            $merged[$qId] = is_array($val) ? $val : [$val];
        }

        // Подсчёт
        $test->load('questions.options');
        $totalQuestions = $test->questions()->count();
        $correctAnswers = 0;

        foreach ($test->questions as $question) {
            $isCorrect = false;

            if ($question->question_type === 'short_answer') {
                // Проверяем текстовый ответ
                $userAnswer = trim($textAnswers[$question->id] ?? '');
                
                if ($userAnswer === '') {
                    $isCorrect = false;
                } else {
                    // Получаем все правильные ответы для этого вопроса
                    $correctOptions = $question->options->where('is_correct', true);
                    
                    foreach ($correctOptions as $option) {
                        $correctText = trim($option->option_text);
                        
                        if ($option->case_insensitive) {
                            // Игнорируем регистр и пробелы
                            if (
                                strtolower(preg_replace('/\s+/', '', $userAnswer)) === 
                                strtolower(preg_replace('/\s+/', '', $correctText))
                            ) {
                                $isCorrect = true;
                                break;
                            }
                        } else {
                            // Точное сравнение
                            if ($userAnswer === $correctText) {
                                $isCorrect = true;
                                break;
                            }
                        }
                    }
                }
            } else {
                // Проверяем выбор вариантов (single_choice или multiple_choice)
                $raw = $merged[$question->id] ?? [];
                // Нормализация: массив целых
                $userOptionIds = collect($raw)
                    ->filter() // убираем null/пустые
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

        // Обновляем активную попытку и сохраняем ответы
        DB::transaction(function () use ($attempt, $score) {
            $attempt->update([
                'score' => round($score),
                'ended_at' => now(),
            ]);

            // Обновляем все ответы этой попытки, добавляя attempt_id если его нет
            TemporaryAnswer::where('user_id', $attempt->user_id)
                ->where('test_id', $attempt->test_id)
                ->whereNull('test_attempt_id')
                ->update(['test_attempt_id' => $attempt->id]);

            // Помечаем все ответы текущей попытки как неактивные
            TemporaryAnswer::where('test_attempt_id', $attempt->id)
                ->update(['is_active' => false]);
        });

        return view('layout', [
            'content' => view('test_result', [
                'test' => $test,
                'score' => round($score),
                'correctAnswers' => $correctAnswers,
                'totalQuestions' => $totalQuestions,
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

        if (!$startedAt) {
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
        if (!Auth::user()->can('edit courses')) {
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
        if (!Auth::user()->can('edit courses')) {
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
                'answers' => $answers,
            ];
        }

        return view('tests.attempt-details', compact('attempt', 'test', 'user', 'course', 'questionDetails'));
    }
}