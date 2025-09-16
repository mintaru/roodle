<?php

namespace App\Http\Controllers;

use App\Models\Lecture;
use App\Models\Course;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;

class LectureController extends Controller
{
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
}
