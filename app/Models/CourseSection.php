<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function visibleGroups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'course_section_groups', 'course_section_id', 'group_id')
            ->withTimestamps();
    }
}

