<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Course;
use Illuminate\Http\Request;

class TestManagementController extends Controller
{
    /**
     * Display a listing of all tests.
     */
    public function index(Request $request)
    {
        $searchColumn = $request->input('search_column', 'title');
        $searchValue = $request->input('search_value', '');

        $query = Test::with('course');

        // Apply search filter
        if ($searchValue) {
            if ($searchColumn === 'title') {
                $query->where('title', 'like', '%' . $searchValue . '%');
            } elseif ($searchColumn === 'id') {
                $query->where('id', 'like', '%' . $searchValue . '%');
            } elseif ($searchColumn === 'course') {
                $query->whereHas('course', function ($q) use ($searchValue) {
                    $q->where('title', 'like', '%' . $searchValue . '%');
                });
            }
        }

        $tests = $query->paginate(15);

        return view('admin.tests.index', compact('tests', 'searchColumn', 'searchValue'));
    }

    /**
     * Show the form for editing the specified test.
     */
    public function edit(Test $test)
    {
        $test->load('course', 'questions');
        $courses = Course::all();
        return view('admin.tests.edit', compact('test', 'courses'));
    }

    /**
     * Update the specified test in storage.
     */
    public function update(Request $request, Test $test)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'max_attempts' => 'nullable|integer|min:0',
        ]);

        $test->update([
            'title' => $request->title,
            'description' => $request->description,
            'course_id' => $request->course_id,
            'max_attempts' => $request->max_attempts ?? 0,
        ]);

        return redirect()->route('admin.tests.index')
            ->with('success', 'Тест успешно обновлён!');
    }

    /**
     * Remove the specified test from storage.
     */
    public function destroy(Test $test)
    {
        // Удаляем все связи с вопросами
        $test->questions()->detach();

        // Удаляем все попытки прохождения
        $test->attempts()->delete();

        // Удаляем все временные ответы
        $test->temporaryAnswers()->delete();

        // Удаляем сам тест
        $test->delete();

        return redirect()->route('admin.tests.index')
            ->with('success', 'Тест успешно удалён!');
    }
}
