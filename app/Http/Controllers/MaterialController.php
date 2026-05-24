<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function create(Course $course)
    {
        return view('materials.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:102400', // 100MB
        ]);

        $file = $request->file('file');
        $path = $file->store('materials', 'public');

        $material = $course->materials()->create([
            'title' => $request->title,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
        ]);

        return redirect()->route('courses.show', $course)->with('success', 'Материал успешно загружен!');
    }

    public function download(Course $course, Material $material)
    {
        if ($material->course_id !== $course->id) {
            abort(404);
        }

        $path = Storage::disk('public')->path($material->file_path);

        if (!file_exists($path)) {
            abort(404, 'Файл не найден');
        }

        return response()->download($path, $material->file_name);
    }

    public function edit(Course $course, Material $material)
    {
        if ($material->course_id !== $course->id) {
            abort(404);
        }

        return view('materials.edit', compact('course', 'material'));
    }

    public function update(Request $request, Course $course, Material $material)
    {
        if ($material->course_id !== $course->id) {
            abort(404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|max:102400',
        ]);

        $material->title = $request->title;

        if ($request->hasFile('file')) {
            // delete old file
            if ($material->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($material->file_path);
            }
            $file = $request->file('file');
            $path = $file->store('materials', 'public');
            $material->file_path = $path;
            $material->file_name = $file->getClientOriginalName();
            $material->file_type = $file->getClientOriginalExtension();
            $material->file_size = $file->getSize();
        }

        $material->save();

        return redirect()->route('courses.show', $course)->with('success', 'Материал обновлён!');
    }

    public function destroy(Course $course, Material $material)
    {
        if ($material->course_id !== $course->id) {
            abort(404);
        }

        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return redirect()->route('courses.show', $course)->with('success', 'Материал удален!');
    }

    public function toggleStatus(Course $course, Material $material)
    {
        if ($material->course_id !== $course->id) {
            abort(404);
        }

        $material->status = $material->status === Material::STATUS_ACTIVE
            ? Material::STATUS_ARCHIVED
            : Material::STATUS_ACTIVE;
        $material->save();

        return redirect()->route('courses.show', $course)->with('success', 'Статус материала изменён!');
    }

    public function archive(Material $material)
    {
        $this->authorize('delete', $material);

        $material->status = Material::STATUS_ARCHIVED;
        $material->save();

        return back()->with('success', 'Материал архивирован');
    }

    public function restore(Material $material)
    {
        $this->authorize('delete', $material);

        $material->status = Material::STATUS_ACTIVE;
        $material->save();

        return back()->with('success', 'Материал восстановлен');
    }
}
