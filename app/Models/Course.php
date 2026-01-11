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

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'course_group', 'course_id', 'group_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isAvailable(): bool
    {
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            return true;
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

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }
}
