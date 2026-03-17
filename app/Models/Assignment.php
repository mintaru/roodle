<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'instructions',
        'due_date',
        'status',
        'position',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVED = 'archived';

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function files()
    {
        return $this->hasMany(AssignmentFile::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }

    public function isOverdue(): bool
    {
        if (!$this->due_date) {
            return false;
        }
        return now()->gt($this->due_date);
    }

    public function getSubmissionByUser($userId): ?AssignmentSubmission
    {
        return $this->submissions()->where('user_id', $userId)->first();
    }
}
