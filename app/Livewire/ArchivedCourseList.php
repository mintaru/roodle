<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class ArchivedCourseList extends Component
{
    public $search = '';

    public function render()
    {
        $user = Auth::user();

        $courses = Course::with('groups', 'author');

        if ($user->hasRole('admin')) {
            $courses->archived();

        } elseif ($user->hasRole('teacher')) {
            $courses->archived()->where('user_id', $user->id);

        } else {
            //НЕЛЬЗЯ ШКОЛЬНИКАМ!!
        }

        // 🔍 Поиск
        if ($this->search) {
            $courses->where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.archived-course-list', [
            'courses' => $courses->latest()->get(),
        ]);
    }
}
