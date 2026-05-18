<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function visibleGroups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'course_section_item_groups', 'course_section_item_id', 'group_id')
            ->withTimestamps();
    }
}

