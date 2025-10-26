<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class CourseList extends Component
{
    public $courses;

    public function mount()
    {
        $user = Auth::user();
    
        if ($user->hasRole('admin')) {
            $this->courses = Course::with('groups', 'author')->get();
        } elseif ($user->hasRole('teacher')) {
            $this->courses = Course::with('groups', 'author')
                ->where('user_id', $user->id)
                ->get();
        } else {
            $groupIds = $user->groups->pluck('id');
            $this->courses = Course::with('groups', 'author')
                ->whereHas('groups', function ($q) use ($groupIds) {
                    $q->whereIn('groups.id', $groupIds);
                })
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.course-list');
    }
}
