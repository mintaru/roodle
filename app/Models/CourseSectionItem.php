<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSectionItem extends Model
{
    protected $fillable = [
        'course_section_id',
        'item_id',
        'item_type',
        'position',
    ];

    public function section()
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function item()
    {
        return $this->morphTo();
    }
}

