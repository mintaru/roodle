<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryAnswer extends Model
{
    use HasFactory;

    /**
     * Массив, содержащий атрибуты, которые можно массово присваивать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'test_id',
        'question_id',
        'option_id',
        'answer_text',
    ];

    /**
     * Отношение "принадлежит одному" с моделью User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Отношение "принадлежит одному" с моделью Test.
     */
    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Отношение "принадлежит одному" с моделью Question.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Отношение "принадлежит одному" с моделью Option.
     */
    public function option()
    {
        return $this->belongsTo(Option::class);
    }

    public function scopeForTest($q, int $testId)
    {
        return $q->where('test_id', $testId);
    }

    public function scopeForUser($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }
}
