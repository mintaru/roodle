<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Search parameters
        $searchColumn = $request->input('search_column', 'title');
        $searchValue = $request->input('search_value', '');

        if ($user->hasRole('admin')) {
            $query = Course::with('groups', 'author');
        } elseif ($user->hasRole('teacher')) {
            $query = Course::with('groups', 'author')
                ->where('user_id', $user->id);
        } else {
            $groupIds = $user->groups->pluck('id');
            $query = Course::with('groups', 'author')
                ->whereHas('groups', function ($q) use ($groupIds) {
                    $q->whereIn('groups.id', $groupIds);
                });
        }

        // Apply search filter
        if ($searchValue) {
            if ($searchColumn === 'title') {
                $query->where('title', 'like', '%'.$searchValue.'%');
            } elseif ($searchColumn === 'id') {
                $query->where('id', 'like', '%'.$searchValue.'%');
            } elseif ($searchColumn === 'author') {
                $query->whereHas('author', function ($q) use ($searchValue) {
                    $q->where('name', 'like', '%'.$searchValue.'%');
                });
            } elseif ($searchColumn === 'description') {
                $query->where('description', 'like', '%'.$searchValue.'%');
            }
        }

        $courses = $query->get();

        return view('courses.index', compact('courses', 'searchColumn', 'searchValue'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = \App\Models\Group::all();

        return view('courses.create', compact('groups')); // вернем Blade форму
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'groups' => 'array',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'status' =>'active'
        ]);

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('courses', 'public');
        }

        // Добавляем автора курса
        $validated['user_id'] = Auth::id();

        $validated['period_start'] = $request->period_start
        ? Carbon::createFromFormat(
            'Y-m-d\TH:i',
            $request->period_start,
            'Asia/Krasnoyarsk'
        )->utc()
        : null;

        $validated['period_end'] = $request->period_end
            ? Carbon::createFromFormat(
                'Y-m-d\TH:i',
                $request->period_end,
                'Asia/Krasnoyarsk'
            )->utc()
            : null;

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
        abort_if(! $course->isAvailable(), 404);

        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('teacher')) {
            // Админ и преподаватель видят все тесты
            $course->load('tests');

            return view('courses.show', compact('course'));
        }
        $course->load([
            'tests' => fn ($query) => $query->available(),
        ]);

        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $groups = \App\Models\Group::all();

        return view('courses.edit', compact('course', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructor' => 'nullable|string|max:255',
            'image_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'groups' => 'array',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
        ]);

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('courses', 'public');
        }

        $validated['period_start'] = $request->period_start
            ? Carbon::createFromFormat(
                'Y-m-d\TH:i',
                $request->period_start,
                'Asia/Krasnoyarsk'
            )->utc()
            : null;

        $validated['period_end'] = $request->period_end
            ? Carbon::createFromFormat(
                'Y-m-d\TH:i',
                $request->period_end,
                'Asia/Krasnoyarsk'
            )->utc()
            : null;

        $course->groups()->sync($request->groups);

        $course->update($validated);

        return redirect()->route('admin.courses.index')->with('success', 'Курс обновлён!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Курс успешно удалён!');
    }

    //*АРХИВИРОВАНИЕ И ВОССТАНОВЛЕНИЕ
    public function archive(Course $course)
    {
        $course->update([
            'status' => Course::STATUS_ARCHIVED,
        ]);

        return back()->with('success', 'Курс отправлен в архив');
    }

    public function restore(Course $course)
    {
        $course->update([
            'status' => Course::STATUS_ACTIVE,
        ]);

        return back()->with('success', 'Курс восстановлен из архива');
    }

    public function archived()
    {
        $courses = Course::archived()->get();
        return view('courses.archived', compact('courses'));
    }
}
