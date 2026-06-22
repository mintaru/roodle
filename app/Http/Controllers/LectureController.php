<?php

namespace App\Http\Controllers;

use App\Helpers\WordToHtmlConverter;
use App\Models\Course;
use App\Models\Lecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

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
                $query->where('title', 'like', '%'.$searchValue.'%');
            } elseif ($searchColumn === 'id') {
                $query->where('id', 'like', '%'.$searchValue.'%');
            } elseif ($searchColumn === 'course') {
                $query->whereHas('course', function ($q) use ($searchValue) {
                    $q->where('title', 'like', '%'.$searchValue.'%');
                });
            } elseif ($searchColumn === 'content') {
                $query->where('content', 'like', '%'.$searchValue.'%');
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

        $addToBank = $request->has('add_to_bank');

        if ($contentSource === 'pdf') {
            $request->validate([
                'title' => 'required|string|max:255',
                'pdf' => 'required|file|mimes:pdf',
                'from_page' => 'nullable|integer|min:1',
                'to_page' => 'nullable|integer|min:1',
            ]);

            $file = $request->file('pdf');
            $path = $file->store('lectures', 'public');

            $parser = new Parser;
            $pdf = $parser->parseFile(Storage::disk('public')->path($path));
            $pages = $pdf->getPages();

            $from = $request->from_page ?: 1;
            $to = $request->to_page ?: count($pages);
            $to = min($to, count($pages));
            $from = max(1, $from);

            $text = '';
            for ($i = $from - 1; $i < $to; $i++) {
                $text .= $pages[$i]->getText()."\n\n";
            }

            $data = [
                'title' => $request->title,
                'pdf_path' => $path,
                'content' => trim($text),
                'content_type' => Lecture::CONTENT_TYPE_TEXT,
            ];
        } elseif ($contentSource === 'word') {
            $request->validate([
                'title' => 'required|string|max:255',
                'word' => 'required|file|mimes:doc,docx',
            ]);

            $file = $request->file('word');

            $path = $file->store('lectures', 'local');

            $fullPath = Storage::disk('local')->path($path);
            $content = WordToHtmlConverter::convert($fullPath);

            if (trim(strip_tags($content)) === '') {
                Storage::disk('local')->delete($path);

                return back()->withErrors(['word' => 'Документ Word пуст или не содержит текста.'])->withInput();
            }

            $data = [
                'title' => $request->title,
                'pdf_path' => $path,
                'content' => $content,
                'content_type' => Lecture::CONTENT_TYPE_HTML,
            ];
        } else {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]);

            $content = $request->input('content');
            if (strip_tags($content) === '' || trim(strip_tags($content)) === '') {
                return back()->withErrors(['content' => 'Введите текст лекции.'])->withInput();
            }

            $data = [
                'title' => $request->title,
                'pdf_path' => null,
                'content' => $content,
                'content_type' => Lecture::CONTENT_TYPE_HTML,
            ];
        }

        if ($addToBank) {
            $data['is_global'] = true;
            $data['user_id'] = null;
            $data['course_id'] = null;
            $lecture = Lecture::create($data);
        } else {
            $data['is_global'] = false;
            $data['user_id'] = $request->user()->id;
            $data['course_id'] = null;
            $lecture = $course->lectures()->create($data);
        }

        return redirect()->route('courses.show', $course)->with('success', 'Лекция создана!');
    }

    public function show(Course $course, Lecture $lecture)
    {
        // Ensure course is accessible for current user (covers archived / not-enrolled cases)
        abort_if(! $course->isAvailable(), 404);

        $user = \Illuminate\Support\Facades\Auth::user();
        if (! $user?->hasAnyRole(['admin', 'teacher']) && ($lecture->status ?? 'active') === Lecture::STATUS_ARCHIVED) {
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
            'pdf' => 'nullable|file|mimes:pdf',
            'word' => 'nullable|file|mimes:doc,docx',
            'from_page' => 'nullable|integer|min:1',
            'to_page' => 'nullable|integer|min:1',
            'content' => 'nullable|string',
        ]);

        if ($contentSource === 'pdf' && $request->hasFile('pdf')) {
            if ($lecture->pdf_path) {
                Storage::disk('public')->delete($lecture->pdf_path);
            }

            $file = $request->file('pdf');
            $path = $file->store('lectures', 'public');

            $parser = new Parser;
            $pdf = $parser->parseFile(Storage::disk('public')->path($path));
            $pages = $pdf->getPages();

            $from = $request->from_page ?: 1;
            $to = $request->to_page ?: count($pages);
            $to = min($to, count($pages));
            $from = max(1, $from);

            $text = '';
            for ($i = $from - 1; $i < $to; $i++) {
                $text .= $pages[$i]->getText()."\n\n";
            }

            $updateData = [
                'title' => $request->title,
                'pdf_path' => $path,
                'content' => trim($text),
                'content_type' => Lecture::CONTENT_TYPE_TEXT,
            ];
        } elseif ($contentSource === 'word' && $request->hasFile('word')) {
            if ($lecture->pdf_path) {
                Storage::disk('local')->exists($lecture->pdf_path)
                    ? Storage::disk('local')->delete($lecture->pdf_path)
                    : Storage::disk('public')->delete($lecture->pdf_path);
            }

            $file = $request->file('word');
            $path = $file->store('lectures', 'local');
            $fullPath = Storage::disk('local')->path($path);
            $content = WordToHtmlConverter::convert($fullPath);

            if (trim(strip_tags($content)) === '') {
                Storage::disk('local')->delete($path);

                return back()->withErrors(['word' => 'Документ Word пуст или не содержит текста.'])->withInput();
            }

            $updateData = [
                'title' => $request->title,
                'pdf_path' => $path,
                'content' => $content,
                'content_type' => Lecture::CONTENT_TYPE_HTML,
            ];
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
        } else {
            $updateData = ['title' => $request->title];
        }

        if ($request->has('add_to_bank')) {
            $updateData['is_global'] = true;
            $updateData['user_id'] = null;
            $updateData['course_id'] = null;
        } else {
            $updateData['is_global'] = false;
            $updateData['user_id'] = $request->user()->id;
            $updateData['course_id'] = null;
        }

        $lecture->update($updateData);

        if ($lecture->course_id) {
            return redirect()->route('courses.show', $lecture->course_id)->with('success', 'Лекция успешно обновлена!');
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
            $url = asset('storage/'.$path);

            return response()->json([
                'url' => $url,
                'path' => $path,
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function serveFile(Course $course, Lecture $lecture)
    {
        // Ensure course is accessible for current user
        abort_if(! $course->isAvailable(), 404);

        if (! $lecture->pdf_path) {
            abort(404);
        }

        // Авторизация — только залогиненные
        if (! \Illuminate\Support\Facades\Auth::check()) {
            abort(403);
        }

        // Файл может быть на local или public диске
        if (Storage::disk('local')->exists($lecture->pdf_path)) {
            $fullPath = Storage::disk('local')->path($lecture->pdf_path);
        } elseif (Storage::disk('public')->exists($lecture->pdf_path)) {
            $fullPath = Storage::disk('public')->path($lecture->pdf_path);
        } else {
            abort(404);
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'doc' => 'application/msword',
        ];
        $mime = $mimeTypes[$extension] ?? 'application/octet-stream';

        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
        ]);
    }
}
