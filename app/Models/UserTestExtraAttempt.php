<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTestExtraAttempt extends Model
{
    protected $table = 'user_test_extra_attempts';

    protected $fillable = [
        'user_id',
        'test_id',
        'extra_attempts',
        'created_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

