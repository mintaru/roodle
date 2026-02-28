<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class CourseList extends Component
{
    public $search = '';

    public function render()
    {
        $user = Auth::user();

        $courses = Course::with('groups', 'author');

        if ($user->hasRole('admin')) {
            $courses->active();

        } elseif ($user->hasRole('teacher')) {
            $courses->active()->where('user_id', $user->id);

        } else {
            $groupIds = $user->groups->pluck('id');

            $courses
                ->active()
                ->whereHas('groups', function ($q) use ($groupIds) {
                    $q->whereIn('groups.id', $groupIds);
                });
        }

        // 🔍 Поиск
        if ($this->search) {
            $courses->where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $courses = $courses->with('groups')->latest()->get();

        // Для студентов фильтруем по isAvailable (учитывает периоды по группам)
        if (! $user->hasRole('admin') && ! $user->hasRole('teacher')) {
            $courses = $courses->filter(fn ($course) => $course->isAvailable());
        }

        return view('livewire.course-list', [
            'courses' => $courses->values(),
        ]);
    }
}
