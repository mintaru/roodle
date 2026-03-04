<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Course extends Model
{
    protected $fillable = ['title', 'description', 'image_path', 'user_id', 'period_start', 'period_end','status'];

    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVED = 'archived';

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];

    public function tests()
    {
        return $this->hasMany(Test::class);
    }

    public function lectures()
    {
        return $this->hasMany(Lecture::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function sections()
    {
        return $this->hasMany(CourseSection::class)
            ->orderBy('position');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'course_group', 'course_id', 'group_id')
            ->withPivot('period_start', 'period_end')
            ->withTimestamps();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Даты в БД хранятся в UTC. Парсим pivot-значение как UTC. */
    private function parseUtc($value): ?\Carbon\Carbon
    {
        if (! $value) {
            return null;
        }
        $raw = $value instanceof \Carbon\Carbon ? $value->format('Y-m-d H:i:s') : (string) $value;

        return \Carbon\Carbon::parse($raw, 'UTC');
    }

    public function isAvailable(?int $groupId = null): bool
    {
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            return true;
        }

        $user = Auth::user();
        if ($user && $user->hasRole('teacher') && $this->user_id === $user->id) {
            return true;
        }

        $now = now()->utc();

        // Для студента: проверяем период доступа по группам пользователя
        if ($user) {
            $userGroupIds = $user->groups->pluck('id');
            $hasGroupInCourse = false;
            foreach ($userGroupIds as $gid) {
                $pivot = $this->groups()->where('group_id', $gid)->first()?->pivot;
                if (! $pivot) {
                    continue;
                }
                $hasGroupInCourse = true;
                $groupStart = $this->parseUtc($pivot->period_start) ?? $this->parseUtc($this->getRawOriginal('period_start'));
                $groupEnd = $this->parseUtc($pivot->period_end) ?? $this->parseUtc($this->getRawOriginal('period_end'));
                $withinStart = ! $groupStart || $now->gte($groupStart);
                $withinEnd = ! $groupEnd || $now->lte($groupEnd);
                if ($withinStart && $withinEnd) {
                    return true;
                }
            }
            if ($hasGroupInCourse) {
                return false;
            }
        }

        // Глобальный период (если пользователь не в группах курса)
        $globalStart = $this->parseUtc($this->getRawOriginal('period_start'));
        $globalEnd = $this->parseUtc($this->getRawOriginal('period_end'));
        if ($globalStart && $now->lt($globalStart)) {
            return false;
        }
        if ($globalEnd && $now->gt($globalEnd)) {
            return false;
        }

        return true;
    }

    public function scopeAvailable($query)
    {
        $now = now();

        return $query
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
        $start = $this->parseUtc($this->getRawOriginal('period_start'));

        return $start ? $start->copy()->setTimezone('Asia/Krasnoyarsk')->format('d.m.Y H:i') : null;
    }

    public function formattedPeriodEnd(): ?string
    {
        $end = $this->parseUtc($this->getRawOriginal('period_end'));

        return $end ? $end->copy()->setTimezone('Asia/Krasnoyarsk')->format('d.m.Y H:i') : null;
    }

    /** Форматирует дату для input datetime-local (Y-m-d\TH:i) в Asia/Krasnoyarsk. */
    public function formatPeriodForInput(string $attribute): string
    {
        $raw = $this->getRawOriginal($attribute);
        $parsed = $this->parseUtc($raw);

        return $parsed ? $parsed->copy()->setTimezone('Asia/Krasnoyarsk')->format('Y-m-d\TH:i') : '';
    }

    /**
     * Возвращает период доступа для текущего пользователя (с учётом группы).
     * Для админа/преподавателя — глобальный период курса.
     * Для студента — период по первой подходящей группе или глобальный.
     */
    public function getEffectivePeriodForUser(?\Illuminate\Contracts\Auth\Authenticatable $user): array
    {
        $start = $this->parseUtc($this->getRawOriginal('period_start'));
        $end = $this->parseUtc($this->getRawOriginal('period_end'));

        if ($user && method_exists($user, 'groups')) {
            $userGroupIds = $user->groups->pluck('id');
            foreach ($userGroupIds as $gid) {
                $pivot = $this->groups()->where('group_id', $gid)->first()?->pivot;
                if ($pivot) {
                    $groupStart = $this->parseUtc($pivot->period_start) ?? $start;
                    $groupEnd = $this->parseUtc($pivot->period_end) ?? $end;

                    return [
                        'start' => $groupStart,
                        'end' => $groupEnd,
                    ];
                }
            }
        }

        return ['start' => $start, 'end' => $end];
    }

    public function formattedPeriodStartForUser(?\Illuminate\Contracts\Auth\Authenticatable $user): ?string
    {
        $period = $this->getEffectivePeriodForUser($user);

        return $period['start']
            ? $period['start']->copy()->setTimezone('Asia/Krasnoyarsk')->format('d.m.Y H:i')
            : null;
    }

    public function formattedPeriodEndForUser(?\Illuminate\Contracts\Auth\Authenticatable $user): ?string
    {
        $period = $this->getEffectivePeriodForUser($user);

        return $period['end']
            ? $period['end']->copy()->setTimezone('Asia/Krasnoyarsk')->format('d.m.Y H:i')
            : null;
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
