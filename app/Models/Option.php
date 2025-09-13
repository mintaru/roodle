<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    /**
     * Отключаем автоматическое добавление полей `timestamps`.
     * В данном случае это не требуется для этой таблицы.
     */
    public $timestamps = false;

    /**
     * Массив, содержащий атрибуты, которые можно массово присваивать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
    ];

    /**
     * Отношение "принадлежит одному" с моделью Question.
     * Один вариант ответа принадлежит одному вопросу.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Отношение "один ко многим" с моделью TemporaryAnswer.
     * Один вариант ответа может быть выбран во многих временных ответах.
     */
    public function temporaryAnswers()
    {
        return $this->hasMany(TemporaryAnswer::class);
    }
}
