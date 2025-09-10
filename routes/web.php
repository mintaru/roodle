<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/courses', function () {
    return view('courses');
});

Route::get('/setup', function () {
    // Создаем таблицу для тестов
    Schema::dropIfExists('answers');
    Schema::dropIfExists('options');
    Schema::dropIfExists('questions');
    Schema::dropIfExists('tests');

    Schema::create('tests', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->timestamps();
    });

    // Создаем таблицу для вопросов
    Schema::create('questions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('test_id')->constrained('tests')->onDelete('cascade');
        $table->text('question_text');
        // Типы вопросов: 'single_choice', 'multiple_choice', 'text'
        $table->string('question_type')->default('single_choice');
        $table->timestamps();
    });

    // Создаем таблицу для вариантов ответов (для вопросов с выбором)
    Schema::create('options', function (Blueprint $table) {
        $table->id();
        $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
        $table->text('option_text');
        $table->boolean('is_correct')->default(false);
        $table->timestamps();
    });

    return 'База данных успешно настроена! <a href="/">Перейти на главную</a>';
});

Route::get('/', function () {
    if (!Schema::hasTable('tests')) {
        return 'Пожалуйста, сначала настройте базу данных, перейдя по адресу <a href="/setup">/setup</a>';
    }
    $tests = DB::table('tests')->orderBy('created_at', 'desc')->get();
    return view('layout', [
        'content' => view('test_list', ['tests' => $tests])
    ]);
});

// Страница с формой для создания нового теста
Route::get('/tests/create', function () {
    return view('layout', [
        'content' => view('test_create_form')
    ]);
});

// Обработка формы создания теста
Route::post('/tests', function () {
    // FIX: Заменяем инъекцию (Request $request) на глобальный хелпер request(),
    // чтобы избежать проблем с внедрением зависимостей.
    request()->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $id = DB::table('tests')->insertGetId([
        'title' => request('title'),
        'description' => request('description'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect('/tests/' . $id);
});

// Страница для просмотра теста и добавления в него вопросов
Route::get('/tests/{testId}', function ($testId) {
    $test = DB::table('tests')->find($testId);
    if (!$test) {
        abort(404);
    }

    $questions = DB::table('questions')
        ->where('test_id', $testId)
        ->get()
        ->map(function ($question) {
            $question->options = DB::table('options')->where('question_id', $question->id)->get();
            return $question;
        });

    return view('layout', [
        'content' => view('test_show', ['test' => $test, 'questions' => $questions])
    ]);
});

// Обработка формы добавления вопроса к тесту
Route::post('/tests/{testId}/questions', function ($testId) {
    // FIX: Здесь также заменяем инъекцию на хелпер request().
    request()->validate([
        'question_text' => 'required|string',
        'options' => 'required|array|min:2',
        'options.*' => 'required|string',
        'correct_option' => 'required|integer',
    ]);

    $questionId = DB::table('questions')->insertGetId([
        'test_id' => $testId,
        'question_text' => request('question_text'),
        'question_type' => 'single_choice', // Упрощенно, только один тип
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    foreach (request('options') as $key => $optionText) {
        DB::table('options')->insert([
            'question_id' => $questionId,
            'option_text' => $optionText,
            'is_correct' => ($key == request('correct_option')),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return back()->with('success', 'Вопрос успешно добавлен!');
});

// Страница для прохождения теста
Route::get('/tests/{testId}/attempt', function ($testId) {
    $test = DB::table('tests')->find($testId);
    if (!$test) {
        abort(404);
    }
    $questions = DB::table('questions')
        ->where('test_id', $testId)
        ->get()
        ->map(function ($question) {
            $question->options = DB::table('options')->where('question_id', $question->id)->inRandomOrder()->get();
            return $question;
        });

    return view('layout', [
        'content' => view('test_attempt', ['test' => $test, 'questions' => $questions])
    ]);
});


// Обработка ответов и подсчет результатов
Route::post('/tests/{testId}/result', function ($testId) {
    // FIX: Заменяем $request->input() на request()->input()
    $answers = request()->input('answers', []);
    $totalQuestions = count($answers);
    $correctAnswers = 0;

    foreach ($answers as $questionId => $optionId) {
        $isCorrect = DB::table('options')
            ->where('id', $optionId)
            ->where('question_id', $questionId)
            ->where('is_correct', true)
            ->exists();

        if ($isCorrect) {
            $correctAnswers++;
        }
    }

    $score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

    $test = DB::table('tests')->find($testId);

    return view('layout', [
        'content' => view('test_result', [
            'test' => $test,
            'score' => round($score),
            'correctAnswers' => $correctAnswers,
            'totalQuestions' => $totalQuestions,
        ])
    ]);
});

require __DIR__.'/auth.php';
