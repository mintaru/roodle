<?php

namespace App\Http\Controllers;

use App\Models\Lecture;
use App\Models\Course;
use App\Helpers\WordToHtmlConverter;
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
            } elseif ($searchColumn === 'content') {
                $query->where('content', 'like', '%' . $searchValue . '%');
            }
        }

        $lectures = $query->paginate(15);

        return view('lectures.index', compact('lectures', 'searchColumn', 'searchValue'));
    }

    public function create(Course $course)
    {
        return view('lectures.create', compact('course'));
    }
//
    public function store(Request $request, Course $course)
    {
        $contentSource = $request->input('content_source', 'manual');

        if ($contentSource === 'pdf') {
            $request->validate([
                'title' => 'required|string|max:255',
                'pdf'   => 'required|file|mimes:pdf',
                'from_page' => 'nullable|integer|min:1',
                'to_page'   => 'nullable|integer|min:1',
            ]);

            $file = $request->file('pdf');
            $path = $file->store('lectures', 'public');

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
                'content' => trim($text),
                'content_type' => Lecture::CONTENT_TYPE_TEXT,
            ]);
        } elseif ($contentSource === 'word') {
            $request->validate([
                'title' => 'required|string|max:255',
                'word'  => 'required|file|mimes:doc,docx',
            ]);

            $file = $request->file('word');
            $filePath = $file->store('lectures/temp', 'local');
            $fullPath = Storage::disk('local')->path($filePath);

            // Конвертируем Word в HTML
            $content = WordToHtmlConverter::convert($fullPath);

            // Удаляем временный файл
            Storage::disk('local')->delete($filePath);

            if (trim(strip_tags($content)) === '') {
                return back()->withErrors(['word' => 'Документ Word пуст или не содержит текста.'])->withInput();
            }

            $lecture = $course->lectures()->create([
                'title' => $request->title,
                'pdf_path' => null,
                'content' => $content,
                'content_type' => Lecture::CONTENT_TYPE_HTML,
            ]);
        } else {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]);

            $content = $request->input('content');
            if (strip_tags($content) === '' || trim(strip_tags($content)) === '') {
                return back()->withErrors(['content' => 'Введите текст лекции.'])->withInput();
            }

            $lecture = $course->lectures()->create([
                'title' => $request->title,
                'pdf_path' => null,
                'content' => $content,
                'content_type' => Lecture::CONTENT_TYPE_HTML,
            ]);
        }

        return redirect()->route('courses.show', $course)->with('success', 'Lecture created!');
    }

    public function show(Course $course, Lecture $lecture)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user?->hasAnyRole(['admin', 'teacher']) && ($lecture->status ?? 'active') === Lecture::STATUS_ARCHIVED) {
            abort(404);
        }
        return view('lectures.show', compact('course', 'lecture'));
    }

    public function edit(Lecture $lecture)
    {
        $lecture->load('course');
        return view('lectures.edit', compact('lecture'));
    }

    public function update(Request $request, Lecture $lecture)
    {
        $contentSource = $request->input('content_source', 'manual');

        $request->validate([
            'title' => 'required|string|max:255',
            'pdf'   => 'nullable|file|mimes:pdf',
            'word'  => 'nullable|file|mimes:doc,docx',
            'from_page' => 'nullable|integer|min:1',
            'to_page'   => 'nullable|integer|min:1',
            'content' => 'nullable|string',
        ]);

        if ($contentSource === 'pdf' && $request->hasFile('pdf')) {
            if ($lecture->pdf_path) {
                Storage::disk('public')->delete($lecture->pdf_path);
            }

            $file = $request->file('pdf');
            $path = $file->store('lectures', 'public');

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
                'content' => trim($text),
                'content_type' => Lecture::CONTENT_TYPE_TEXT,
            ]);
        } elseif ($contentSource === 'word' && $request->hasFile('word')) {
            if ($lecture->pdf_path) {
                Storage::disk('public')->delete($lecture->pdf_path);
            }

            $file = $request->file('word');
            $filePath = $file->store('lectures/temp', 'local');
            $fullPath = Storage::disk('local')->path($filePath);

            // Конвертируем Word в HTML
            $content = WordToHtmlConverter::convert($fullPath);

            // Удаляем временный файл
            Storage::disk('local')->delete($filePath);

            if (trim(strip_tags($content)) === '') {
                return back()->withErrors(['word' => 'Документ Word пуст или не содержит текста.'])->withInput();
            }

            $lecture->update([
                'title' => $request->title,
                'pdf_path' => null,
                'content' => $content,
                'content_type' => Lecture::CONTENT_TYPE_HTML,
            ]);
        } elseif ($contentSource === 'manual') {
            $content = $request->input('content');
            if ($content === null || strip_tags($content) === '' || trim(strip_tags($content)) === '') {
                return back()->withErrors(['content' => 'Введите текст лекции.'])->withInput();
            }

            $updateData = [
                'title' => $request->title,
                'content' => $content,
                'content_type' => Lecture::CONTENT_TYPE_HTML,
            ];
            if ($lecture->pdf_path) {
                Storage::disk('public')->delete($lecture->pdf_path);
                $updateData['pdf_path'] = null;
            }
            $lecture->update($updateData);
        } else {
            // PDF или Word выбраны но нет нового файла - просто обновляем название
            $lecture->update(['title' => $request->title]);
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

    public function archive(Lecture $lecture)
    {
        $lecture->update([
            'status' => Lecture::STATUS_ARCHIVED,
        ]);

        return back()->with('success', 'Лекция отправлена в архив');
    }

    public function restore(Lecture $lecture)
    {
        $lecture->update([
            'status' => Lecture::STATUS_ACTIVE,
        ]);

        return back()->with('success', 'Лекция восстановлена из архива');
    }

    public function uploadAttachment(Request $request)
    {
        $request->validate([
            'attachment' => 'required|image|max:5120', // 5MB max
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('lecture-images', 'public');
            $url = asset('storage/' . $path);

            return response()->json([
                'url' => $url,
                'path' => $path,
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
