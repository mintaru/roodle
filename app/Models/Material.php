<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'status',
        'user_id',
        'is_global',
    ];

    protected $casts = [
        'is_global' => 'boolean',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVED = 'archived';

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function sectionItems()
    {
        return $this->morphMany(CourseSectionItem::class, 'item');
    }

    public function getLinkedCoursesAttribute()
    {
        return $this->sectionItems->map(fn($si) => $si->section->course)->filter()->unique('id')->values();
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }
}
