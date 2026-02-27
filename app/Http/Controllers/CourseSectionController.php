<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseSectionItem;
use App\Models\Lecture;
use App\Models\Test;
use Illuminate\Http\Request;

class CourseSectionController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $maxPosition = $course->sections()->max('position') ?? 0;

        $course->sections()->create([
            'title' => $data['title'],
            'position' => $maxPosition + 1,
        ]);

        return back()->with('success', 'Секция добавлена');
    }

    public function update(Request $request, Course $course, CourseSection $section)
    {
        abort_unless($section->course_id === $course->id, 404);

        $data = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $section->update($data);

        return back()->with('success', 'Секция обновлена');
    }

    public function destroy(Course $course, CourseSection $section)
    {
        abort_unless($section->course_id === $course->id, 404);

        $section->delete();

        return back()->with('success', 'Секция удалена');
    }

    public function move(Request $request, Course $course, CourseSection $section)
    {
        abort_unless($section->course_id === $course->id, 404);

        $direction = $request->input('direction');

        if (!in_array($direction, ['up', 'down'], true)) {
            return back();
        }

        $operator = $direction === 'up' ? '<' : '>';
        $orderBy = $direction === 'up' ? 'desc' : 'asc';

        $swapWith = $course->sections()
            ->where('position', $operator, $section->position)
            ->orderBy('position', $orderBy)
            ->first();

        if ($swapWith) {
            $currentPos = $section->position;
            $section->update(['position' => $swapWith->position]);
            $swapWith->update(['position' => $currentPos]);
        }

        return back();
    }

    public function attachItem(Request $request, Course $course, CourseSection $section)
    {
        abort_unless($section->course_id === $course->id, 404);

        $data = $request->validate([
            'item_type' => 'required|string|in:test,lecture',
            'item_id' => 'required|integer',
        ]);

        if ($data['item_type'] === 'test') {
            $item = Test::where('course_id', $course->id)->findOrFail($data['item_id']);
        } else {
            $item = Lecture::where('course_id', $course->id)->findOrFail($data['item_id']);
        }

        $existing = CourseSectionItem::where('item_type', get_class($item))
            ->where('item_id', $item->id)
            ->first();

        if ($existing && $existing->course_section_id === $section->id) {
            return back();
        }

        if ($existing) {
            $existing->delete();
        }

        $maxPosition = $section->items()->max('position') ?? 0;

        CourseSectionItem::create([
            'course_section_id' => $section->id,
            'item_type' => get_class($item),
            'item_id' => $item->id,
            'position' => $maxPosition + 1,
        ]);

        return back()->with('success', 'Элемент добавлен в секцию');
    }

    public function moveItem(Request $request, Course $course, CourseSection $section, CourseSectionItem $item)
    {
        abort_unless($section->course_id === $course->id, 404);
        abort_unless($item->course_section_id === $section->id, 404);

        $direction = $request->input('direction');

        if (!in_array($direction, ['up', 'down'], true)) {
            return back();
        }

        $operator = $direction === 'up' ? '<' : '>';
        $orderBy = $direction === 'up' ? 'desc' : 'asc';

        $swapWith = $section->items()
            ->where('position', $operator, $item->position)
            ->orderBy('position', $orderBy)
            ->first();

        if ($swapWith) {
            $currentPos = $item->position;
            $item->update(['position' => $swapWith->position]);
            $swapWith->update(['position' => $currentPos]);
        }

        return back();
    }

    public function detachItem(Course $course, CourseSection $section, CourseSectionItem $item)
    {
        abort_unless($section->course_id === $course->id, 404);
        abort_unless($item->course_section_id === $section->id, 404);

        $item->delete();

        return back()->with('success', 'Элемент удалён из секции');
    }
}

