<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\AssignmentFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function create(Course $course)
    {
        $this->authorize('edit courses');
        return view('assignments.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $this->authorize('edit courses');

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
            'files.*' => 'nullable|file|max:102400', // 100MB per file
        ]);

        $assignment = $course->assignments()->create([
            'title' => $request->title,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'due_date' => $request->due_date,
            'position' => $course->assignments()->count(),
        ]);

        // Handle file uploads for assignment
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('assignments', 'public');
                
                AssignmentFile::create([
                    'assignment_id' => $assignment->id,
                    'title' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('courses.show', $course)->with('success', 'Задание успешно создано!');
    }

    public function show(Course $course, Assignment $assignment)
    {
        if ($assignment->course_id !== $course->id) {
            abort(404);
        }

        $this->authorize('edit courses');

        $submissions = $assignment->submissions()
            ->with('user', 'files')
            ->get();

        return view('assignments.show', compact('course', 'assignment', 'submissions'));
    }

    public function edit(Course $course, Assignment $assignment)
    {
        if ($assignment->course_id !== $course->id) {
            abort(404);
        }

        $this->authorize('edit courses');
        return view('assignments.edit', compact('course', 'assignment'));
    }

    public function update(Request $request, Course $course, Assignment $assignment)
    {
        if ($assignment->course_id !== $course->id) {
            abort(404);
        }

        $this->authorize('edit courses');

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
            'files.*' => 'nullable|file|max:102400',
        ]);

        $assignment->update([
            'title' => $request->title,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'due_date' => $request->due_date,
        ]);

        // Handle new file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('assignments', 'public');
                
                AssignmentFile::create([
                    'assignment_id' => $assignment->id,
                    'title' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('assignments.show', [$course, $assignment])
            ->with('success', 'Задание успешно обновлено!');
    }

    public function destroy(Course $course, Assignment $assignment)
    {
        if ($assignment->course_id !== $course->id) {
            abort(404);
        }

        $this->authorize('edit courses');

        // Delete all associated files
        foreach ($assignment->files as $file) {
            Storage::disk('public')->delete($file->file_path);
        }

        // Delete all submission files
        foreach ($assignment->submissions as $submission) {
            foreach ($submission->files as $file) {
                Storage::disk('public')->delete($file->file_path);
            }
        }

        $assignment->delete();

        return redirect()->route('courses.show', $course)
            ->with('success', 'Задание удалено!');
    }

    public function archive(Course $course, Assignment $assignment)
    {
        if ($assignment->course_id !== $course->id) {
            abort(404);
        }

        $this->authorize('edit courses');

        $assignment->update(['status' => Assignment::STATUS_ARCHIVED]);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Задание архивировано');
    }

    public function restore(Course $course, Assignment $assignment)
    {
        if ($assignment->course_id !== $course->id) {
            abort(404);
        }

        $this->authorize('edit courses');

        $assignment->update(['status' => Assignment::STATUS_ACTIVE]);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Задание восстановлено');
    }

    public function deleteFile(Course $course, Assignment $assignment, AssignmentFile $file)
    {
        if ($assignment->course_id !== $course->id || $file->assignment_id !== $assignment->id) {
            abort(404);
        }

        $this->authorize('edit courses');

        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return back()->with('success', 'Файл удалён');
    }

    public function downloadFile(Course $course, Assignment $assignment, AssignmentFile $file)
    {
        if ($assignment->course_id !== $course->id || $file->assignment_id !== $assignment->id) {
            abort(404);
        }

        $path = Storage::disk('public')->path($file->file_path);

        if (!file_exists($path)) {
            abort(404, 'Файл не найден');
        }

        return response()->download($path, $file->file_name);
    }

    public function move(Request $request, Course $course, Assignment $assignment)
    {
        if ($assignment->course_id !== $course->id) {
            abort(404);
        }

        $this->authorize('edit courses');

        $direction = $request->input('direction');
        $assignments = $course->assignments()->orderBy('position')->get();
        $currentIndex = $assignments->search(function ($item) use ($assignment) {
            return $item->id === $assignment->id;
        });

        if ($currentIndex === false) {
            return back();
        }

        if ($direction === 'up' && $currentIndex > 0) {
            $assignments[$currentIndex - 1]->update(['position' => $currentIndex]);
            $assignment->update(['position' => $currentIndex - 1]);
        } elseif ($direction === 'down' && $currentIndex < $assignments->count() - 1) {
            $assignments[$currentIndex + 1]->update(['position' => $currentIndex]);
            $assignment->update(['position' => $currentIndex + 1]);
        }

        return back();
    }
}
