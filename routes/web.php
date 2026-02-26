<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController; // Импортируем наш созданный контроллер
use App\Models\Test; // Импортируем модель Test
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CourseController;
use App\Models\Lecture;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\Admin\GroupUserController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\Admin\TestManagementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\QuestionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::get('/', function () {
    return view('courses');
})->middleware('auth')->name("home");
Route::get('/courses/archived', [CourseController::class, 'archived'])->name('courses.archived');

Route::get('/courses/create', [CourseController::class, 'create'])->middleware('auth')->name('courses.create');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');


// Маршрут для настройки базы данных

// --- Маршруты для управления тестами (используя TestController) ---

// Главная страница: отображает список всех тестов
// Перенаправлен на метод index() в TestController

// Страница создания нового теста
// Перенаправлен на метод create() в TestController
Route::get('/courses/{course}/tests/create', [TestController::class, 'create'])->middleware('auth')->name('tests.create');

// Обработка формы создания теста (POST-запрос)
// Перенаправлен на метод store() в TestController
Route::post('/courses/{course}/tests', [TestController::class, 'store'])->name('tests.store');

Route::get('/tests/{test}/view', [TestController::class, 'view'])->middleware('auth')->name('tests.view');

// Страница просмотра одного теста (включая вопросы)
// Eloquent автоматически найдет тест по ID благодаря Route Model Binding
// Перенаправлен на метод show() в TestController
Route::get('/tests/{test}', [TestController::class, 'show'])->middleware('auth')->name('tests.show');

// Обработка добавления нового вопроса к тесту (POST-запрос)
// Перенаправлен на метод storeQuestion() в TestController
Route::post('/tests/{test}/questions', [TestController::class, 'storeQuestion'])->name('tests.store_question');

// Удаление вопроса из теста (DELETE-запрос)
Route::delete('/tests/{test}/questions/{question}', [TestController::class, 'removeQuestion'])->name('tests.removeQuestion');

// Просмотр результатов теста (обзор тестирования)
Route::get('/tests/{test}/results', [TestController::class, 'results'])->middleware('auth')->name('tests.results');

// Просмотр деталей попытки ученика
Route::get('/test-attempts/{attempt}/details', [TestController::class, 'viewAttemptDetails'])->middleware('auth')->name('test-attempts.details');

// Ручная проверка развёрнутых ответов в попытке
Route::post('/test-attempts/{attempt}/grade-rich-text', [TestController::class, 'gradeRichTextAnswers'])
    ->middleware('auth')
    ->name('test-attempts.grade-rich-text');

// Выдача дополнительных попыток для ученика (по тесту и пользователю)
Route::post('/tests/{test}/users/{user}/grant-attempts', [TestController::class, 'grantExtraAttempts'])
    ->middleware('auth')
    ->name('test-attempts.grant-attempts');

// Эти маршруты показывают, как можно их вынести в контроллер для единообразия.

// Страница для начала прохождения теста
Route::get('/tests/{test}/attempt', function (Test $test) { // Используем Route Model Binding
    $user = Auth::user();

    // Проверка количества попыток
    if ($test->max_attempts > 0) {
        $completedAttempts = $test->attempts()
            ->where('user_id', $user->id)
            ->whereNotNull('ended_at')
            ->count();

        // Получаем дополнительные попытки для пользователя
        $extraAttempts = \App\Models\UserTestExtraAttempt::where('user_id', $user->id)
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
    if (!$activeAttempt) {
        $lastAttemptNumber = \App\Models\TestAttempt::where('test_id', $test->id)
            ->where('user_id', $user->id)
            ->max('attempt_number') ?? 0;

        $activeAttempt = $test->attempts()->create([
            'user_id' => $user->id,
            'score' => 0,
            'attempt_number' => $lastAttemptNumber + 1,
            'started_at' => now(),
        ]);
    } else if (!$activeAttempt->started_at) {
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

    if (!$questionOrder) {
        $questionOrder = $questions->pluck('id')->toArray();

        if ($test->randomize_questions) {
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
    $tempAnswers = \App\Models\TemporaryAnswer::where('test_attempt_id', $activeAttempt->id)
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
        } else {
            // Для множественного выбора сохраняем как массив option_id
            if (!isset($savedAnswers[$answer->question_id])) {
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
            'attempt' => $activeAttempt
        ])
    ]);
})->name('tests.attempt');

Route::get('/tests/{test}/attempt/{questionIndex?}', function (Test $test, $questionIndex = 1) {
    $user = Auth::user();

    // Проверка количества попыток
    if ($test->max_attempts > 0) {
        $completedAttempts = $test->attempts()
            ->where('user_id', $user->id)
            ->whereNotNull('ended_at')
            ->count();

        // Получаем дополнительные попытки для пользователя
        $extraAttempts = \App\Models\UserTestExtraAttempt::where('user_id', $user->id)
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
    if (!$activeAttempt) {
        $lastAttemptNumber = \App\Models\TestAttempt::where('test_id', $test->id)
            ->where('user_id', $user->id)
            ->max('attempt_number') ?? 0;

        $activeAttempt = $test->attempts()->create([
            'user_id' => $user->id,
            'score' => 0,
            'attempt_number' => $lastAttemptNumber + 1,
            'started_at' => now(),
        ]);
    } elseif (!$activeAttempt->started_at) {
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

    if (!$questionOrder) {
        $questionOrder = $questions->pluck('id')->toArray();

        if ($test->randomize_questions) {
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

    // Проверка выхода за пределы
    if ($questionIndex < 1) $questionIndex = 1;
    if ($questionIndex > $questions->count()) $questionIndex = $questions->count();

    $question = $questions[$questionIndex - 1]; // текущий вопрос
    
    // Загружаем сохраненные ответы только для текущей активной попытки и только активные
    $tempAnswers = \App\Models\TemporaryAnswer::where('test_attempt_id', $activeAttempt->id)
        ->where('is_active', true)
        ->get();

    // Загружаем сохраненные ответы только для текущей активной попытки и только активные
    $tempAnswers = \App\Models\TemporaryAnswer::where('test_attempt_id', $activeAttempt->id)
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
        } else {
            // Для множественного выбора сохраняем как массив option_id
            if (!isset($savedAnswers[$answer->question_id])) {
                $savedAnswers[$answer->question_id] = [];
            }
            if ($answer->option_id) {
                $savedAnswers[$answer->question_id][] = $answer->option_id;
            }
        }
    }

    return view('layout', [
        'content' => view('test_attempt_page', [
            'test' => $test,
            'question' => $question,
            'questionIndex' => $questionIndex,
            'totalQuestions' => $questions->count(),
            'savedAnswers' => $savedAnswers,
        ])
    ]);
})->middleware('auth')->name('tests.attempt.page');


// Обработка сохранения временного ответа (AJAX)
Route::post('/tests/{test}/save-answer', function (Test $test) {
    
    $questionId = request('question_id');
    $optionIds = request('option_id');
    $answerText = request('answer_text');
    $richTextAnswer = request('rich_text_answer');

    // Для текстовых ответов
    if (request()->has('answer_text')) {
        // Сохраняем в сессии
        $answers = session("test_{$test->id}_answers", []);
        $answers[$questionId] = $answerText;
        session(["test_{$test->id}_answers" => $answers]);

        if (Auth::check()) {
            $userId = Auth::id();

            // Получаем текущую попытку
            $attempt = \App\Models\TestAttempt::where('test_id', $test->id)
                        ->where('user_id', $userId)
                        ->whereNull('ended_at')
                        ->first();

            if (!$attempt) {
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
            \App\Models\TemporaryAnswer::where('user_id', $userId)
                ->where('test_id', $test->id)
                ->where('question_id', $questionId)
                ->delete();

            // Вставляем новый текстовый ответ
            \App\Models\TemporaryAnswer::create([
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
    if (request()->has('rich_text_answer')) {
        // Сохраняем в сессии
        $answers = session("test_{$test->id}_answers", []);
        $answers[$questionId] = $richTextAnswer;
        session(["test_{$test->id}_answers" => $answers]);

        if (Auth::check()) {
            $userId = Auth::id();

            // Получаем текущую попытку
            $attempt = \App\Models\TestAttempt::where('test_id', $test->id)
                        ->where('user_id', $userId)
                        ->whereNull('ended_at')
                        ->first();

            if (!$attempt) {
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
            \App\Models\TemporaryAnswer::where('user_id', $userId)
                ->where('test_id', $test->id)
                ->where('question_id', $questionId)
                ->delete();

            // Вставляем новый развёрнутый ответ
            \App\Models\TemporaryAnswer::create([
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
    if (!request()->has('answer_text') && $optionIds) {
        $optionIds = (array) $optionIds;

        // Сохраняем в сессии
        $answers = session("test_{$test->id}_answers", []);
        $answers[$questionId] = $optionIds;
        session(["test_{$test->id}_answers" => $answers]);

        if (Auth::check()) {
            $userId = Auth::id();

            // Получаем текущую попытку
            $attempt = \App\Models\TestAttempt::where('test_id', $test->id)
                        ->where('user_id', $userId)
                        ->whereNull('ended_at')
                        ->first();

            if (!$attempt) {
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
            \App\Models\TemporaryAnswer::where('user_id', $userId)
                ->where('test_id', $test->id)
                ->where('question_id', $questionId)
                ->delete();

            // Вставляем новый(е) ответ(ы)
            foreach ($optionIds as $optionId) {
                \App\Models\TemporaryAnswer::create([
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
})->name('tests.save_answer');

// Синхронизация таймера между устройствами
Route::get('/tests/{test}/timer-sync', [TestController::class, 'timerSync'])
    ->middleware('auth')
    ->name('tests.timer_sync');

// Обработка отправки ответов и подсчета результатов
Route::post('/tests/{test}/result', [TestController::class, 'result'])
    ->middleware('auth')
    ->name('tests.result');

Route::get('/courses/{course}/lectures/create', [LectureController::class, 'create'])->middleware('auth')->name('lectures.create');
Route::post('/courses/{course}/lectures', [LectureController::class, 'store'])->middleware('auth')->name('lectures.store');


// Новый маршрут для просмотра лекции через курс
Route::get('/courses/{course}/lectures/{lecture}', [LectureController::class, 'show'])->middleware('auth')->name('lectures.show');
// Подключение маршрутов аутентификации Laravel
Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');


Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->middleware('auth')->name('courses.edit'); // форма редактирования
Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');

Route::post('/tests/{test}/add-from-bank', [TestController::class, 'addFromBank'])->name('tests.add_from_bank');

Route::middleware(['auth', 'role:admin|teacher'])->group(function () {
    //для групп
    Route::get('/admin/groups', [GroupUserController::class, 'index'])->name('admin.groups.index');
    Route::get('/admin/groups/create', [GroupUserController::class, 'create'])->name('admin.groups.create');
    Route::post('/admin/groups', [GroupUserController::class, 'store'])->name('admin.groups.store');
    Route::get('/admin/groups/{group}', [GroupUserController::class, 'show'])->name('admin.groups.show');
    Route::post('/admin/groups/{group}/assign', [GroupUserController::class, 'assign'])->name('admin.groups.assign');
    Route::delete('/admin/groups/{group}', [GroupUserController::class, 'destroy'])->name('admin.groups.destroy');
    Route::put('/admin/groups/{group}', [GroupUserController::class, 'update'])->name('admin.groups.update');
    Route::delete('/admin/groups/{group}/remove/{user}', [GroupUserController::class, 'remove'])->name('admin.groups.remove');

    //для юзеров
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');

    //для курсов
    Route::get('/admin/courses', [CourseController::class, 'index'])->name('admin.courses.index');
    Route::delete('/admin/courses/{course}', [CourseController::class, 'destroy'])->name('admin.courses.destroy');

    //АРХИВИРОВАНИЕ
    Route::patch('/courses/{course}/archive', [CourseController::class, 'archive'])->name('courses.archive');
    Route::patch('/courses/{course}/restore', [CourseController::class, 'restore'])->name('courses.restore');


    // Маршруты для управления лекциями
    Route::get('/lectures', [LectureController::class, 'index'])->name('admin.lectures.index');
    Route::get('/lectures/{lecture}/edit', [LectureController::class, 'edit'])->name('admin.lectures.edit');
    Route::put('/lectures/{lecture}', [LectureController::class, 'update'])->name('admin.lectures.update');
    Route::delete('/lectures/{lecture}', [LectureController::class, 'destroy'])->name('admin.lectures.destroy');
    
    // Маршруты для банка вопросов
    Route::get('/question-bank', [QuestionBankController::class, 'index'])->name('admin.question-bank.index');
    Route::get('/question-bank/{question}/edit', [QuestionBankController::class, 'edit'])->name('admin.question-bank.edit');
    Route::put('/question-bank/{question}', [QuestionBankController::class, 'update'])->name('admin.question-bank.update');
    Route::delete('/question-bank/{question}', [QuestionBankController::class, 'destroy'])->name('admin.question-bank.destroy');
    
    // Маршруты для управления тестами
    Route::get('/tests', [TestManagementController::class, 'index'])->name('admin.tests.index');
    Route::get('/tests/{test}/edit', [TestManagementController::class, 'edit'])->name('admin.tests.edit');
    Route::put('/tests/{test}', [TestManagementController::class, 'update'])->name('admin.tests.update');
    Route::delete('/tests/{test}', [TestManagementController::class, 'destroy'])->name('admin.tests.destroy');
    Route::get('/tests/{id}/attempts', [TestController::class, 'attempts'])->name('admin.tests.attempts');

    Route::post('/questions/upload', [QuestionController::class, 'upload'])->name('questions.upload');

});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Маршруты для отчётов
    Route::get('/reports', function () {
        return view('admin.reports.index');
    })->name('admin.reports.index');
    Route::get('/reports/user-activity', [ReportController::class, 'userActivity'])->name('admin.reports.user-activity');
    Route::get('/reports/groups', [ReportController::class, 'groupsReport'])->name('admin.reports.groups');
    Route::get('/reports/courses', [ReportController::class, 'coursesReport'])->name('admin.reports.courses');
    



});




require __DIR__.'/auth.php';
