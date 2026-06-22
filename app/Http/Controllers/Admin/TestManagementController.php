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
            } elseif ($searchColumn === 'description') {
                $query->where('description', 'like', '%' . $searchValue . '%');
            } elseif ($searchColumn === 'max_attempts') {
                $query->where('max_attempts', 'like', '%' . $searchValue . '%');
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
            'course_id' => 'nullable|exists:courses,id',
            'max_attempts' => 'nullable|integer|min:0',
            'time_limit' => 'nullable|integer|min:0'
        ]);

        $addToBank = $request->has('add_to_bank');

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'max_attempts' => $request->max_attempts ?? 0,
        ];

        if ($addToBank) {
            $data['is_global'] = true;
            $data['user_id'] = null;
            $data['course_id'] = null;
        } else {
            $data['is_global'] = false;
            $data['course_id'] = $request->course_id;
        }

        $test->update($data);

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

    public function archive(Test $test)
    {
        $test->update([
            'status' => Test::STATUS_ARCHIVED,
        ]);

        return back()->with('success', 'Тест отправлен в архив');
    }

    public function restore(Test $test)
    {
        $test->update([
            'status' => Test::STATUS_ACTIVE,
        ]);

        return back()->with('success', 'Тест восстановлен из архива');
    }
}
