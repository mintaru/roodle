<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Course;

class CourseList extends Component
{
    public $courses;

    public function mount()
    {
        $this->courses = Course::all();
    }

    public function render()
    {
        return view('livewire.course-list');
    }
}
