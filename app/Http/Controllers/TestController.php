<?php

namespace App\Http\Controllers;

use App\Models\{Test, Question, Option, Course};
use Illuminate\Http\Request;

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
            'content' => view('test_create_form', ['course' => $course])
        ]);
    }

    // Сохраняем новый тест для курса
    public function store(Request $request, Course $course)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $test = $course->tests()->create($validatedData);

        return redirect()->route('tests.show', $test);
    }

    // Отображение теста с вопросами
    public function show(Test $test)
    {
        $test->load('questions.options');
        return view('layout', [
            'content' => view('test_show', ['test' => $test])
        ]);
    }

    /**
     * Сохраняет новый вопрос для теста.
     */
    public function storeQuestion(Request $request, Test $test)
    {
        $validatedData = $request->validate([
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_option' => 'required|integer',
        ]);

        $question = $test->questions()->create([
            'question_text' => $validatedData['question_text'],
            'question_type' => 'single_choice',
        ]);

        foreach ($validatedData['options'] as $key => $optionText) {
            $question->options()->create([
                'option_text' => $optionText,
                'is_correct' => ($key == $validatedData['correct_option']),
            ]);
        }

        return back()->with('success', 'Вопрос успешно добавлен!');
    }
}
