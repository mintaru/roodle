<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController; // Импортируем наш созданный контроллер
use App\Models\Test; // Импортируем модель Test
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\CourseController;
use App\Models\Lecture;
use App\Http\Controllers\LectureController;
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

// Пример маршрута для курсов (если он вам нужен)
Route::get('/courses', function () {
    return view('courses');
});

Route::get('/', function () {
    return view('courses');
});

Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');


// Маршрут для настройки базы данных
Route::get('/setup', function () {
    // Удаление таблиц в правильном порядке (сначала зависимые)
    Schema::dropIfExists('temporary_answers');
    Schema::dropIfExists('options');
    Schema::dropIfExists('questions');
    Schema::dropIfExists('tests');

    // Создание таблиц
    Schema::create('tests', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->timestamps();
    });

    Schema::create('questions', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->id();
        $table->foreignId('test_id')->constrained('tests')->onDelete('cascade');
        $table->text('question_text');
        $table->string('question_type')->default('single_choice');
        $table->timestamps();
    });

    Schema::create('options', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->id();
        $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
        $table->text('option_text');
        $table->boolean('is_correct')->default(false);
        $table->timestamps();
    });

    Schema::create('temporary_answers', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('test_id')->constrained('tests')->onDelete('cascade');
        $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
        $table->foreignId('option_id')->constrained('options')->onDelete('cascade');
        $table->timestamps();
    });

    return 'База данных успешно настроена! <a href="/">Перейти на главную</a>';
});

// --- Маршруты для управления тестами (используя TestController) ---

// Главная страница: отображает список всех тестов
// Перенаправлен на метод index() в TestController

// Страница создания нового теста
// Перенаправлен на метод create() в TestController
Route::get('/courses/{course}/tests/create', [TestController::class, 'create'])->name('tests.create');

// Обработка формы создания теста (POST-запрос)
// Перенаправлен на метод store() в TestController
Route::post('/courses/{course}/tests', [TestController::class, 'store'])->name('tests.store');

// Страница просмотра одного теста (включая вопросы)
// Eloquent автоматически найдет тест по ID благодаря Route Model Binding
// Перенаправлен на метод show() в TestController
Route::get('/tests/{test}', [TestController::class, 'show'])->name('tests.show');

// Обработка добавления нового вопроса к тесту (POST-запрос)
// Перенаправлен на метод storeQuestion() в TestController
Route::post('/tests/{test}/questions', [TestController::class, 'storeQuestion'])->name('tests.store_question');


// --- Маршруты для прохождения теста (требуют доработки в TestController) ---
// Эти маршруты показывают, как можно их вынести в контроллер для единообразия.

// Страница для начала прохождения теста
Route::get('/tests/{test}/attempt', function (Test $test) { // Используем Route Model Binding
    // Eager loading для перемешивания вопросов и опций
    $test->load(['questions' => function ($query) {
        $query->with(['options' => function ($query) {
            $query->inRandomOrder(); // Перемешиваем опции
        }]);
    }]);

    // Загружаем сохраненные ответы пользователя из сессии
    $savedAnswers = session("test_{$test->id}_answers", []);

    return view('layout', [
        'content' => view('test_attempt', [
            'test' => $test,
            'savedAnswers' => $savedAnswers
        ])
    ]);
})->name('tests.attempt');

// Обработка сохранения временного ответа (AJAX)
Route::post('/tests/{test}/save-answer', function (Test $test) { // Используем Route Model Binding
    $questionId = request('question_id');
    $optionId = request('option_id');

    // Сохраняем ответ в сессии
    $answers = session("test_{$test->id}_answers", []);
    $answers[$questionId] = $optionId;
    session(["test_{$test->id}_answers" => $answers]);

    // Если пользователь авторизован, сохраняем также в БД
    if (auth()->check()) {
        $userId = auth()->id();

        // Удаляем предыдущий ответ на этот вопрос, если он существует
        \App\Models\TemporaryAnswer::where('user_id', $userId)
            ->where('test_id', $test->id)
            ->where('question_id', $questionId)
            ->delete();

        // Вставляем новый временный ответ
        \App\Models\TemporaryAnswer::create([
            'user_id' => $userId,
            'test_id' => $test->id,
            'question_id' => $questionId,
            'option_id' => $optionId,
        ]);
    }

    return response()->json(['success' => true]);
})->name('tests.save_answer');

// Обработка отправки ответов и подсчета результатов
Route::post('/tests/{test}/result', function (Test $test) { // Используем Route Model Binding
    $answers = request()->input('answers', []); // Ответы из формы
    $totalQuestions = count($answers);
    $correctAnswers = 0;

    // Загружаем правильные ответы для теста
    $test->load(['questions.options' => function ($query) {
        $query->where('is_correct', true); // Получаем только правильные опции
    }]);

    foreach ($answers as $questionId => $optionId) {
        $question = $test->questions->find($questionId); // Находим вопрос
        if ($question) {
            $correctOption = $question->options->first(); // Получаем правильный вариант (если он есть)
            // Сравниваем ID выбранного ответа с ID правильного ответа
            if ($correctOption && $correctOption->id == $optionId) {
                $correctAnswers++;
            }
        }
    }

    $score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

    return view('layout', [
        'content' => view('test_result', [
            'test' => $test,
            'score' => round($score),
            'correctAnswers' => $correctAnswers,
            'totalQuestions' => $totalQuestions,
        ])
    ]);
})->middleware('auth')->name('tests.result');

Route::get('/courses/{course}/lectures/create', [LectureController::class, 'create'])->name('lectures.create');
Route::post('/courses/{course}/lectures', [LectureController::class, 'store'])->name('lectures.store');


// Новый маршрут для просмотра лекции через курс
Route::get('/courses/{course}/lectures/{lecture}', [LectureController::class, 'show'])->name('lectures.show');
// Подключение маршрутов аутентификации Laravel
Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index'); // список
Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit'); // форма редактирования
Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');

require __DIR__.'/auth.php';
