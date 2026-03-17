<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentSubmissionFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AssignmentSubmissionController extends Controller
{
    public function view(Course $course, Assignment $assignment)
    {
        if ($assignment->course_id !== $course->id) {
            abort(404);
        }

        $submission = $assignment->getSubmissionByUser(Auth::id());
        
        // Get all submissions for teachers/admins
        $submissions = collect();
        if (Auth::user()->hasAnyRole(['teacher', 'admin'])) {
            $submissions = $assignment->submissions()
                ->with('user', 'files')
                ->get();
        }

        return view('assignments.view', compact('course', 'assignment', 'submission', 'submissions'));
    }

    public function submit(Request $request, Course $course, Assignment $assignment)
    {
        if ($assignment->course_id !== $course->id) {
            abort(404);
        }

        $request->validate([
            'answer_text' => 'nullable|string',
            'files.*' => 'nullable|file|max:102400', // 100MB per file
        ]);

        // Get or create submission
        $submission = AssignmentSubmission::firstOrCreate(
            [
                'assignment_id' => $assignment->id,
                'user_id' => Auth::id(),
            ]
        );

        $submission->update([
            'answer_text' => $request->input('answer_text'),
            'submitted_at' => now(),
        ]);

        // Handle file uploads for submission
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('assignment-submissions', 'public');
                
                AssignmentSubmissionFile::create([
                    'assignment_submission_id' => $submission->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()
            ->route('assignments.view', [$course, $assignment])
            ->with('success', 'Ответ успешно отправлен!');
    }

    public function downloadSubmissionFile(Course $course, Assignment $assignment, AssignmentSubmission $submission, AssignmentSubmissionFile $file)
    {
        if ($assignment->course_id !== $course->id || $submission->assignment_id !== $assignment->id || $file->assignment_submission_id !== $submission->id) {
            abort(404);
        }

        // Check authorization
        if (Auth::id() !== $submission->user_id && !Auth::user()->hasAnyRole(['teacher', 'admin'])) {
            abort(403);
        }

        $path = Storage::disk('public')->path($file->file_path);

        if (!file_exists($path)) {
            abort(404, 'Файл не найден');
        }

        return response()->download($path, $file->file_name);
    }

    public function deleteSubmissionFile(Course $course, Assignment $assignment, AssignmentSubmission $submission, AssignmentSubmissionFile $file)
    {
        if ($assignment->course_id !== $course->id || $submission->assignment_id !== $assignment->id || $file->assignment_submission_id !== $submission->id) {
            abort(404);
        }

        // Only the student can delete their own files, or teacher during grading
        if (Auth::id() !== $submission->user_id && !Auth::user()->hasAnyRole(['teacher', 'admin'])) {
            abort(403);
        }

        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return back()->with('success', 'Файл удалён');
    }

    public function grade(Request $request, Course $course, Assignment $assignment, AssignmentSubmission $submission)
    {
        if ($assignment->course_id !== $course->id || $submission->assignment_id !== $assignment->id) {
            abort(404);
        }

        $this->authorize('edit courses');

        $request->validate([
            'score' => 'required|numeric|min:0',
            'teacher_comment' => 'nullable|string',
        ]);

        $submission->update([
            'score' => $request->input('score'),
            'teacher_comment' => $request->input('teacher_comment'),
            'graded_at' => now(),
        ]);

        return back()->with('success', 'Оценка выставлена!');
    }
}
