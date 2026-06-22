<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'course_id',
        'user_id',
        'is_global',
        'title',
        'description',
        'instructions',
        'due_date',
        'status',
        'position',
    ];

    protected $casts = [
        'due_date' => 'datetime',
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
        return $this->belongsTo(User::class);
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

    public function createCopyForUser(User $user): self
    {
        $copy = $this->replicate(['is_global', 'user_id', 'course_id']);

        $copy->is_global = false;
        $copy->user_id = $user->id;
        $copy->course_id = null;
        $copy->save();

        foreach ($this->files as $file) {
            $copy->files()->create([
                'title' => $file->title,
                'file_path' => $file->file_path,
                'file_name' => $file->file_name,
                'file_type' => $file->file_type,
                'file_size' => $file->file_size,
            ]);
        }

        return $copy;
    }
}
