<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    /**
     * Массив, содержащий атрибуты, которые можно массово присваивать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'test_id',
        'question_text',
        'question_type',
    ];

    /**
     * Отношение "принадлежит одному" с моделью Test.
     * Один вопрос принадлежит одному тесту.
     */
    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Отношение "один ко многим" с моделью Option.
     * Один вопрос может иметь много вариантов ответов.
     */
    public function options()
    {
        return $this->hasMany(Option::class);
    }

    /**
     * Отношение "один ко многим" с моделью TemporaryAnswer.
     * Один вопрос может иметь много временных ответов.
     */
    public function temporaryAnswers()
    {
        return $this->hasMany(TemporaryAnswer::class);
    }
}
