<?php

namespace App\Models;

use App\Models\CourseSectionItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Test extends Model
{
    use HasFactory;

    /**
     * Массив, содержащий атрибуты, которые можно массово присваивать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id',
        'is_global',
        'max_attempts',
        'time_limit',
        'period_start',
        'period_end',
        'randomize_questions',
        'display_mode',
        'is_details_available',
    ];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'randomize_questions' => 'boolean',
        'is_details_available' => 'boolean',
        'is_global' => 'boolean',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVED = 'archived';

    /**
     * Отношение "один ко многим" с моделью Question.
     * Один тест может иметь много вопросов.
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'test_question')
            ->withPivot('question_order', 'page_number')
            ->withTimestamps();
    }

    /**
     * Отношение "один ко многим" с моделью TemporaryAnswer.
     * Один тест может иметь много временных ответов (для разных пользователей).
     */
    public function temporaryAnswers()
    {
        return $this->hasMany(TemporaryAnswer::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function sectionItems()
    {
        return $this->morphMany(CourseSectionItem::class, 'item');
    }

    public function getLinkedCoursesAttribute()
    {
        return $this->sectionItems->map(fn($si) => $si->section->course)->filter()->unique('id')->values();
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function attempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function extraAttempts()
    {
        return $this->hasMany(UserTestExtraAttempt::class);
    }

    public function isAvailable(): bool
    {
        if (Auth::check() && Auth::user()->hasAnyRole(['admin', 'teacher'])) {
            return true;
        }

        // If the parent course is not available for the current user, test is not available
        if ($this->course && method_exists($this->course, 'isAvailable') && ! $this->course->isAvailable()) {
            return false;
        }

        if ($this->status === self::STATUS_ARCHIVED) {
            return false;
        }

        $now = now();

        if ($this->period_start && $now->lt($this->period_start)) {
            return false;
        }

        if ($this->period_end && $now->gt($this->period_end)) {
            return false;
        }

        return true;
    }

    public function scopeAvailable($query)
    {
        $now = now();

        return $query
            ->where('status', self::STATUS_ACTIVE)
            ->where(function ($q) use ($now) {
                $q->whereNull('period_start')
                    ->orWhere('period_start', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('period_end')
                    ->orWhere('period_end', '>=', $now);
            });
    }

    public function formattedPeriodStart(): ?string
{
    return $this->period_start
        ? $this->period_start->setTimezone('Asia/Krasnoyarsk')->format('d.m.Y H:i')
        : null;
}

public function formattedPeriodEnd(): ?string
{
    return $this->period_end
        ? $this->period_end->setTimezone('Asia/Krasnoyarsk')->format('d.m.Y H:i')
        : null;
}


/**
 * Получает максимально допустимое количество попыток для пользователя (базовые + дополнительные)
 */
public function getMaxAttemptsForUser($userId): int
{
    if ($this->max_attempts <= 0) {
        return 0; // Неограниченное количество
    }

    $extraAttempts = UserTestExtraAttempt::where('user_id', $userId)
        ->where('test_id', $this->id)
        ->first();

    return $this->max_attempts + ($extraAttempts ? $extraAttempts->extra_attempts : 0);
}
}

