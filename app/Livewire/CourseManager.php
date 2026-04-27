<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseSectionItem;
use App\Models\Lecture;
use App\Models\Material;
use App\Models\Test;
use Livewire\Component;
use Livewire\Attributes\Validate;

class CourseManager extends Component
{
    public Course $course;

    #[Validate('required|string|max:255')]
    public string $newSectionTitle = '';

    public $editingSectionId = null;
    public $editingSectionTitle = '';

    public $successMessage = '';
    public $errorMessage = '';

    public function mount(Course $course)
    {
        $this->course = $course;
        $this->course->load(['sections.items', 'tests', 'lectures', 'materials']);
    }

    public function addSection()
    {
        $this->validate();

        try {
            $maxPosition = $this->course->sections()->max('position') ?? 0;

            $this->course->sections()->create([
                'title' => $this->newSectionTitle,
                'position' => $maxPosition + 1,
            ]);

            $this->newSectionTitle = '';
            $this->successMessage = 'Секция добавлена';
            $this->course->load(['sections.items.item', 'tests', 'lectures', 'materials']);

            $this->dispatch('flash-success', message: 'Секция добавлена');
        } catch (\Exception $e) {
            $this->errorMessage = 'Ошибка при добавлении секции';
        }
    }

    public function editSection($sectionId)
    {
        $section = $this->course->sections()->find($sectionId);
        if ($section) {
            $this->editingSectionId = $sectionId;
            $this->editingSectionTitle = $section->title;
        }
    }

    public function updateSection()
    {
        $this->validate(['editingSectionTitle' => 'required|string|max:255']);

        try {
            $section = $this->course->sections()->find($this->editingSectionId);
            if ($section) {
                $section->update(['title' => $this->editingSectionTitle]);
                $this->editingSectionId = null;
                $this->editingSectionTitle = '';
                $this->successMessage = 'Секция обновлена';
                $this->course->load(['sections.items.item', 'tests', 'lectures', 'materials']);
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Ошибка при обновлении секции';
        }
    }

    public function cancelEdit()
    {
        $this->editingSectionId = null;
        $this->editingSectionTitle = '';
    }

    public function deleteSection($sectionId)
    {
        try {
            $section = $this->course->sections()->find($sectionId);
            if ($section) {
                $section->delete();
                $this->successMessage = 'Секция удалена';
                $this->course->load(['sections.items.item', 'tests', 'lectures', 'materials']);
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Ошибка при удалении секции';
        }
    }

    public function moveSection($sectionId, $direction)
    {
        if (!in_array($direction, ['up', 'down'], true)) {
            return;
        }

        try {
            $section = $this->course->sections()->find($sectionId);
            if (!$section) {
                return;
            }

            $operator = $direction === 'up' ? '<' : '>';
            $orderBy = $direction === 'up' ? 'desc' : 'asc';

            $swapWith = $this->course->sections()
                ->where('position', $operator, $section->position)
                ->orderBy('position', $orderBy)
                ->first();

            if ($swapWith) {
                $currentPos = $section->position;
                $section->update(['position' => $swapWith->position]);
                $swapWith->update(['position' => $currentPos]);
                $this->course->load(['sections.items.item', 'tests', 'lectures', 'materials']);
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Ошибка при перемещении секции';
        }
    }

    public function attachItem($sectionId, $itemType, $itemId)
    {
        if (!$itemId) {
            return; // Пользователь выбрал пустой вариант
        }

        try {
            $section = $this->course->sections()->find($sectionId);
            if (!$section) {
                throw new \Exception('Секция не найдена');
            }

            if ($itemType === 'test') {
                $item = Test::where('course_id', $this->course->id)->findOrFail($itemId);
                if (($item->status ?? 'active') === Test::STATUS_ARCHIVED) {
                    throw new \Exception('Нельзя добавить архивный тест в секцию');
                }
            } elseif ($itemType === 'lecture') {
                $item = Lecture::where('course_id', $this->course->id)->findOrFail($itemId);
                if (($item->status ?? 'active') === Lecture::STATUS_ARCHIVED) {
                    throw new \Exception('Нельзя добавить архивную лекцию в секцию');
                }
            } else {
                $item = Material::where('course_id', $this->course->id)->findOrFail($itemId);
                if (($item->status ?? 'active') === Material::STATUS_ARCHIVED) {
                    throw new \Exception('Нельзя добавить архивный материал в секцию');
                }
            }

            $existing = CourseSectionItem::where('item_type', get_class($item))
                ->where('item_id', $item->id)
                ->first();

            if ($existing && $existing->course_section_id === $section->id) {
                throw new \Exception('Этот элемент уже в этой секции');
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

            $this->successMessage = 'Элемент добавлен в секцию';
            $this->course->load(['sections.items.item', 'tests', 'lectures', 'materials']);
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function moveItem($itemId, $direction)
    {
        if (!in_array($direction, ['up', 'down'], true)) {
            return;
        }

        try {
            $item = CourseSectionItem::with('section')->find($itemId);
            if (!$item) {
                return;
            }

            $section = $item->section;

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
                $this->course->load(['sections.items.item', 'tests', 'lectures', 'materials']);
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Ошибка при перемещении элемента';
        }
    }

    public function detachItem($itemId)
    {
        try {
            $item = CourseSectionItem::with('section')->find($itemId);
            if ($item) {
                $item->delete();
                $this->successMessage = 'Элемент удалён из секции';
                $this->course->load(['sections.items.item', 'tests', 'lectures', 'materials']);
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Ошибка при удалении элемента';
        }
    }

    public function render()
    {
        return view('livewire.course-manager', [
            'sections' => $this->course->sections()->orderBy('position')->get(),
        ]);
    }
}
