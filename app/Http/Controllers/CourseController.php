<?php

namespace App\Http\Controllers;
use App\Models\Course;

use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('courses.create'); // вернем Blade форму
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'instructor'  => 'nullable|string|max:255',
            'image_path'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // загрузка картинки если есть
        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('courses', 'public');
        }

        Course::create($validated);

        return redirect()->route('courses.create')->with('success', 'Курс успешно создан!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        // Eager loading для тестов
        $course->load('tests');

        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
