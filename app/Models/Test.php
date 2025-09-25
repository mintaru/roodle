<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'max_attempts', // Добавляем max_attempts в массив fillable
    ];

    /**
     * Отношение "один ко многим" с моделью Question.
     * Один тест может иметь много вопросов.
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'test_question')
            ->withPivot('question_order')
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

    public function attempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

}
