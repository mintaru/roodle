<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        ]);

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

    // Отображение теста с вопросами
    public function show(Test $test)
    {
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

        $userAttempts = $test->attempts()->where('user_id', $user->id)->count();
        $remaining = $test->max_attempts == 0
            ? '∞'
            : max(0, $test->max_attempts - $userAttempts);

        return view('tests.view', compact('test', 'userAttempts', 'remaining'));
    }

    /**
     * Сохраняет новый вопрос для теста.
     */
    public function storeQuestion(Request $request, Test $test)
    {
        // Валидация
        $validatedData = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:single_choice,multiple_choice',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_option' => 'required_if:question_type,single_choice|integer',
            'correct_options' => 'required_if:question_type,multiple_choice|array',
            'correct_options.*' => 'integer',
        ]);

        // Создаём вопрос
        $question = Question::create([
            'question_text' => $validatedData['question_text'],
            'question_type' => $validatedData['question_type'],
        ]);

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
            ]);
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

        // 🔹 1. Проверяем количество попыток
        if ($test->max_attempts > 0) {
            $userAttempts = $test->attempts()->where('user_id', $user->id)->count();

            if ($userAttempts >= $test->max_attempts) {
                return redirect()->route('tests.show', $test)
                    ->with('error', 'Вы исчерпали все попытки для этого теста.');
            }
        }

        // 🔹 2. Подсчёт результата
        $answers = $request->input('answers', []);
        $totalQuestions = $test->questions()->count();
        $correctAnswers = 0;

        $test->load('questions.options');

        foreach ($test->questions as $question) {
            $userOptionIds = collect($answers[$question->id] ?? [])
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
                    $correctAnswers++;
                }
            } elseif ($question->question_type === 'multiple_choice') {
                if ($userOptionIds === $correctOptionIds) {
                    $correctAnswers++;
                }
            }
        }

        $score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

        // 🔹 3. Создаём запись в таблице попыток
        $test->attempts()->create([
            'user_id' => $user->id,
            'score' => round($score),
        ]);

        // 🔹 4. Показываем результат
        return view('layout', [
            'content' => view('test_result', [
                'test' => $test,
                'score' => round($score),
                'correctAnswers' => $correctAnswers,
                'totalQuestions' => $totalQuestions,
            ]),
        ]);
    }
}
