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
    ];

    /**
     * Отношение "один ко многим" с моделью Question.
     * Один тест может иметь много вопросов.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Отношение "один ко многим" с моделью TemporaryAnswer.
     * Один тест может иметь много временных ответов (для разных пользователей).
     */
    public function temporaryAnswers()
    {
        return $this->hasMany(TemporaryAnswer::class);
    }
}
