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
Route::post('/tests/{test}/save-answer', function (Test $test) {
    $questionId = request('question_id');
    $optionIds = request('option_id');

    // Убедимся, что $optionIds всегда массив
    $optionIds = (array) $optionIds;

    // Сохраняем в сессии
    $answers = session("test_{$test->id}_answers", []);
    $answers[$questionId] = $optionIds;
    session(["test_{$test->id}_answers" => $answers]);

    if (auth()->check()) {
        $userId = auth()->id();

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
                'question_id' => $questionId,
                'option_id' => $optionId,
            ]);
        }
    }

    return response()->json(['success' => true]);
})->name('tests.save_answer');


// Обработка отправки ответов и подсчета результатов
Route::post('/tests/{test}/result', [TestController::class, 'result'])
    ->middleware('auth')
    ->name('tests.result');

Route::get('/courses/{course}/lectures/create', [LectureController::class, 'create'])->name('lectures.create');
Route::post('/courses/{course}/lectures', [LectureController::class, 'store'])->name('lectures.store');


// Новый маршрут для просмотра лекции через курс
Route::get('/courses/{course}/lectures/{lecture}', [LectureController::class, 'show'])->name('lectures.show');
// Подключение маршрутов аутентификации Laravel
Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index'); // список
Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit'); // форма редактирования
Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');

Route::post('/tests/{test}/add-from-bank', [TestController::class, 'addFromBank'])->name('tests.add_from_bank');

require __DIR__.'/auth.php';
