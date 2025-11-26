<?php

namespace App\Http\Controllers;

use App\Models\Lecture;
use App\Models\Course;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;

class LectureController extends Controller
{
    public function index(Request $request)
    {
        $searchColumn = $request->input('search_column', 'title');
        $searchValue = $request->input('search_value', '');

        $query = Lecture::with('course');

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

        $lectures = $query->get();

        return view('lectures.index', compact('lectures', 'searchColumn', 'searchValue'));
    }

    public function create(Course $course)
    {
        return view('lectures.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'pdf'   => 'required|file|mimes:pdf',
            'from_page' => 'nullable|integer|min:1',
            'to_page'   => 'nullable|integer|min:1',
        ]);

        $file = $request->file('pdf');
        $path = $file->store('lectures', 'public');

        // Парсим PDF
        $parser = new Parser();
        $pdf = $parser->parseFile(Storage::disk('public')->path($path));
        $pages = $pdf->getPages();

        $from = $request->from_page ?: 1;
        $to   = $request->to_page ?: count($pages);
        $to   = min($to, count($pages));
        $from = max(1, $from);

        $text = '';
        for ($i = $from - 1; $i < $to; $i++) {
            $text .= $pages[$i]->getText() . "\n\n";
        }

        $lecture = $course->lectures()->create([
            'title' => $request->title,
            'pdf_path' => $path,
            'content' => $text,
        ]);

        return redirect()->route('courses.show', $course)->with('success', 'Lecture created!');
    }

    public function show(Course $course, Lecture $lecture)
    {
        return view('lectures.show', compact('course', 'lecture'));
    }

    public function edit(Lecture $lecture)
    {
        $lecture->load('course');
        return view('lectures.edit', compact('lecture'));
    }

    public function update(Request $request, Lecture $lecture)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'pdf'   => 'nullable|file|mimes:pdf',
            'from_page' => 'nullable|integer|min:1',
            'to_page'   => 'nullable|integer|min:1',
        ]);

        if ($request->hasFile('pdf')) {
            // Удаляем старый файл
            if ($lecture->pdf_path) {
                Storage::disk('public')->delete($lecture->pdf_path);
            }

            $file = $request->file('pdf');
            $path = $file->store('lectures', 'public');

            // Парсим PDF
            $parser = new Parser();
            $pdf = $parser->parseFile(Storage::disk('public')->path($path));
            $pages = $pdf->getPages();

            $from = $request->from_page ?: 1;
            $to   = $request->to_page ?: count($pages);
            $to   = min($to, count($pages));
            $from = max(1, $from);

            $text = '';
            for ($i = $from - 1; $i < $to; $i++) {
                $text .= $pages[$i]->getText() . "\n\n";
            }

            $lecture->update([
                'title' => $request->title,
                'pdf_path' => $path,
                'content' => $text,
            ]);
        } else {
            $lecture->update([
                'title' => $request->title,
            ]);
        }

        return redirect()->route('admin.lectures.index')->with('success', 'Лекция успешно обновлена!');
    }

    public function destroy(Lecture $lecture)
    {
        // Удаляем файл PDF
        if ($lecture->pdf_path) {
            Storage::disk('public')->delete($lecture->pdf_path);
        }

        $lecture->delete();

        return redirect()->route('admin.lectures.index')->with('success', 'Лекция успешно удалена!');
    }
}
