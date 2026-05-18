<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\CourseSectionItem;
use App\Models\Lecture;
use App\Models\Material;
use App\Models\Test;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Assignment;

class CourseManager extends Component
{
    public Course $course;

    public array $visibilityGroupIds = [];

    public string $visibilityType = '';

    public ?int $visibilityTargetId = null;

    #[Validate('required|string|max:255')]
    public string $newSectionTitle = '';

    public $editingSectionId = null;

    public $editingSectionTitle = '';

    public $successMessage = '';

    public $errorMessage = '';

    public function mount(Course $course)
    {
        $this->course = $course;
        $this->course->load(['sections.items', 'tests', 'lectures', 'materials', 'assignments']);
    }

    public function addSection()
    {
        $this->validate();

        try {
            $maxPosition = $this->course->sections()->max('position') ?? 0;

            $section = $this->course->sections()->create([
                'title' => $this->newSectionTitle,
                'position' => $maxPosition + 1,
            ]);
            $section->visibleGroups()->sync(
                $this->course->groups->pluck('id')->toArray()
            );

            $this->newSectionTitle = '';
            $this->successMessage = 'Секция добавлена';
            $this->course->load(['sections.items.item', 'tests', 'lectures', 'materials','assignments']);

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
                $this->course->load(['sections.items.item', 'tests', 'lectures', 'materials','assignments']);
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
                $this->course->load(['sections.items.item', 'tests', 'lectures', 'materials','assignments']);
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Ошибка при удалении секции';
        }
    }

    #[Renderless]
    public function getAttachData(int $sectionId): array
    {
        $sec = $this->course->sections->find($sectionId);
        if (! $sec) {
            return ['tests' => [], 'lectures' => [], 'materials' => [], 'assignments' => []];
        }

        $addedTestIds = $sec->items()->where('item_type', \App\Models\Test::class)->pluck('item_id')->toArray();
        $addedLectureIds = $sec->items()->where('item_type', \App\Models\Lecture::class)->pluck('item_id')->toArray();
        $addedMaterialIds = $sec->items()->where('item_type', \App\Models\Material::class)->pluck('item_id')->toArray();
        $addedAssignmentIds = $sec->items()->where('item_type', \App\Models\Assignment::class)->pluck('item_id')->toArray();

        return [
            'tests' => $this->course->tests->where('status', \App\Models\Test::STATUS_ACTIVE)
                ->whereNotIn('id', $addedTestIds)->map(fn ($t) => ['id' => $t->id, 'title' => $t->title])->values()->toArray(),
            'lectures' => $this->course->lectures->where('status', \App\Models\Lecture::STATUS_ACTIVE)
                ->whereNotIn('id', $addedLectureIds)->map(fn ($l) => ['id' => $l->id, 'title' => $l->title])->values()->toArray(),
            'materials' => $this->course->materials->where('status', \App\Models\Material::STATUS_ACTIVE)
                ->whereNotIn('id', $addedMaterialIds)->map(fn ($m) => ['id' => $m->id, 'title' => $m->title])->values()->toArray(),
            'assignments' => $this->course->assignments->where('status', \App\Models\Assignment::STATUS_ACTIVE)
                ->whereNotIn('id', $addedAssignmentIds)->map(fn ($a) => ['id' => $a->id, 'title' => $a->title])->values()->toArray(),
        ];
    }

    public function moveSection($sectionId, $direction)
    {
        if (! in_array($direction, ['up', 'down'], true)) {
            return;
        }

        try {
            $section = $this->course->sections()->find($sectionId);
            if (! $section) {
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
                $this->course->load(['sections.items.item', 'tests', 'lectures', 'materials','assignments']);
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Ошибка при перемещении секции';
        }
    }

    public function attachItem($sectionId, $itemType, $itemId)
    {
        if (! $itemId) {
            return; // Пользователь выбрал пустой вариант
        }

        try {
            $section = $this->course->sections()->find($sectionId);
            if (! $section) {
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
            } elseif ($itemType === 'assignment') {
                $item = \App\Models\Assignment::where('course_id', $this->course->id)->findOrFail($itemId);
                if (($item->status ?? 'active') === \App\Models\Assignment::STATUS_ARCHIVED) {
                    throw new \Exception('Нельзя добавить архивное задание в секцию');
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

            $courseSectionItem = CourseSectionItem::create([
                'course_section_id' => $section->id,
                'item_type' => get_class($item),
                'item_id' => $item->id,
                'position' => $maxPosition + 1,
            ]);
            $courseSectionItem->visibleGroups()->sync(
                $this->course->groups->pluck('id')->toArray()
            );

            $this->successMessage = 'Элемент добавлен в секцию';
            $this->course->load(['sections.items.item', 'tests', 'lectures', 'materials','assignments']);
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function moveItem($itemId, $direction)
    {
        if (! in_array($direction, ['up', 'down'], true)) {
            return;
        }

        try {
            $item = CourseSectionItem::with('section')->find($itemId);
            if (! $item) {
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
                $this->course->load(['sections.items.item', 'tests', 'lectures', 'materials','assignments']);
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
                $this->course->load(['sections.items.item', 'tests', 'lectures', 'materials','assignments']);
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Ошибка при удалении элемента';
        }
    }

    public function render()
    {
        $sections = $this->course->sections()
            ->orderBy('position')
            ->with(['items.item', 'visibleGroups', 'items.visibleGroups'])
            ->get();

        return view('livewire.course-manager', [
            'sections' => $sections,
            'userGroupIds' => auth()->user()?->groups->pluck('id')->toArray() ?? [],
            'isTeacherOrAdmin' => auth()->user()?->hasAnyRole(['teacher', 'admin']) ?? false,
        ]);
    }

    /**
     * Открыть модалку видимости для секции.
     * Вызывается из JS: @this.openSectionVisibility(id)
     */
    public function openSectionVisibility(int $sectionId): void
    {
        $section = \App\Models\CourseSection::findOrFail($sectionId);
        abort_unless($section->course_id === $this->course->id, 403);

        $this->visibilityType = 'section';
        $this->visibilityTargetId = $sectionId;
        $this->visibilityGroupIds = $section->visibleGroups()->pluck('groups.id')->map(fn ($id) => (string) $id)->toArray();

        $this->dispatch('open-visibility-modal');
    }

    /**
     * Открыть модалку видимости для элемента секции.
     * Вызывается из JS: @this.openItemVisibility(id)
     */
    public function openItemVisibility(int $sectionItemId): void
    {
        $item = \App\Models\CourseSectionItem::findOrFail($sectionItemId);
        abort_unless($item->section->course_id === $this->course->id, 403);

        $this->visibilityType = 'item';
        $this->visibilityTargetId = $sectionItemId;
        $this->visibilityGroupIds = $item->visibleGroups()->pluck('groups.id')->map(fn ($id) => (string) $id)->toArray();

        $this->dispatch('open-visibility-modal');
    }

    /**
     * Сохранить настройки видимости.
     */
    public function saveVisibility(): void
    {
        $groupIds = array_map('intval', $this->visibilityGroupIds);

        // Валидируем: все group_id должны принадлежать курсу
        $allowedGroupIds = $this->course->groups()->pluck('groups.id')->toArray();
        $groupIds = array_intersect($groupIds, $allowedGroupIds);

        if ($this->visibilityType === 'section') {
            $section = \App\Models\CourseSection::findOrFail($this->visibilityTargetId);
            abort_unless($section->course_id === $this->course->id, 403);
            $section->visibleGroups()->sync($groupIds);
        } elseif ($this->visibilityType === 'item') {
            $item = \App\Models\CourseSectionItem::findOrFail($this->visibilityTargetId);
            abort_unless($item->section->course_id === $this->course->id, 403);
            $item->visibleGroups()->sync($groupIds);
        }

        $this->visibilityGroupIds = [];
        $this->visibilityTargetId = null;
        $this->visibilityType = '';
        $this->successMessage = 'Настройки видимости сохранены';

        $this->dispatch('close-visibility-modal');
    }
}
