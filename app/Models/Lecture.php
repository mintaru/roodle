<?php

namespace App\Models;

use App\Models\CourseSectionItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_id',
        'title',
        'content',
        'content_type',
        'attachments',
        'pdf_path',
        'status',
        'user_id',
        'is_global',
    ];

    const CONTENT_TYPE_TEXT = 'text';
    const CONTENT_TYPE_HTML = 'html';

    protected $casts = [
        'attachments' => 'array',
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
