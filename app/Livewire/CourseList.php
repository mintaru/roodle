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
            $courses = $courses->active();

        } elseif ($user->hasRole('teacher')) {
            // Учитель видит свои курсы + курсы с правами доступа
            $courses = $courses->active()->where(function ($q) use ($user) {
                // Свои курсы (где user_id = текущий пользователь)
                $q->where('user_id', $user->id)
                    // ИЛИ курсы, к которым есть права доступа
                    ->orWhereHas('permittedTeachers', function ($q2) use ($user) {
                        $q2->where('users.id', $user->id);
                    });
            });

        } else {
            $groupIds = $user->groups->pluck('id');

            $courses = $courses
                ->active()
                ->whereHas('groups', function ($q) use ($groupIds) {
                    $q->whereIn('groups.id', $groupIds);
                });
        }

        // 🔍 Поиск
        if ($this->search) {
            $courses = $courses->where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            });
        }

        $courses = $courses->latest()->get();

        // Для студентов фильтруем по isAvailable (учитывает периоды по группам)
        if (! $user->hasRole('admin') && ! $user->hasRole('teacher')) {
            $courses = $courses->filter(fn ($course) => $course->isAvailable());
        }

        return view('livewire.course-list', [
            'courses' => $courses->values(),
        ]);
    }
}
