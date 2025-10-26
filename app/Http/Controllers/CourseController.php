<?php

namespace App\Http\Controllers;
use App\Models\Course;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
    
        if ($user->hasRole('admin')) {
            $courses = Course::with('groups', 'author')->get();
        } elseif ($user->hasRole('teacher')) {
            $courses = Course::with('groups', 'author')
                ->where('user_id', $user->id)
                ->get();
        } else {
            $groupIds = $user->groups->pluck('id');
            $courses = Course::with('groups', 'author')
                ->whereHas('groups', function ($q) use ($groupIds) {
                    $q->whereIn('groups.id', $groupIds);
                })
                ->get();
        }
    
        return view('courses.index', compact('courses'));
    }
    
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = \App\Models\Group::all();
        return view('courses.create', compact('groups'));// вернем Blade форму
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'image_path'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'groups'      => 'array',
        ]);
    
        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('courses', 'public');
        }
    
        // Добавляем автора курса
        $validated['user_id'] = Auth::id();
    
        $course = Course::create($validated);
    
        // Привязываем выбранные группы
        if ($request->has('groups')) {
            $course->groups()->sync($request->groups);
        }
    
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
    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'instructor'  => 'nullable|string|max:255',
            'image_path'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('courses', 'public');
        }

        $course->update($validated);

        return redirect()->route('courses.index')->with('success', 'Курс обновлён!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
