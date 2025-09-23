<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_text',
        'question_type',
    ];

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function temporaryAnswers()
    {
        return $this->hasMany(TemporaryAnswer::class);
    }


    public function tests()
    {
        return $this->belongsToMany(Test::class, 'test_question')
            ->withPivot('question_order')
            ->withTimestamps();
    }
}
