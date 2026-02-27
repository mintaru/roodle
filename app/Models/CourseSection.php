<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'position',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function items()
    {
        return $this->hasMany(CourseSectionItem::class)
            ->orderBy('position');
    }
}

