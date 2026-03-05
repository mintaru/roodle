<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherCoursePermission extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'can_edit',
        'can_delete',
        'can_manage_students',
    ];

    protected $casts = [
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
        'can_manage_students' => 'boolean',
    ];

    /**
     * Получить учителя, которому принадлежит права доступа
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Получить курс, к которому применяются права доступа
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
