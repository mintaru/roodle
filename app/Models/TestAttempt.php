<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'test_id',
        'score',
        'attempt_number',
        'started_at',
        'ended_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(TemporaryAnswer::class, 'test_attempt_id');
    }

    public function index()
    {
        $users = TestAttempt::all(); // получаем все записи из таблицы

        return view('users.index', compact('users'));
    }
}
