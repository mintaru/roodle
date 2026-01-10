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
            // admin видит всё

        } elseif ($user->hasRole('teacher')) {
            $courses->where('user_id', $user->id);

        } else {
            $groupIds = $user->groups->pluck('id');

            $courses
                ->available() // 👈 ВАЖНО
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

        return view('livewire.course-list', [
            'courses' => $courses->latest()->get(),
        ]);
    }
}
