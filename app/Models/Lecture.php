<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'content',
        'content_type',
        'attachments',
        'pdf_path',
        'status',
    ];

    const CONTENT_TYPE_TEXT = 'text';
    const CONTENT_TYPE_HTML = 'html';

    protected $casts = [
        'attachments' => 'array',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVED = 'archived';

    public function course()
    {
        return $this->belongsTo(Course::class);
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
