<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['title', 'description', 'instructor', 'image_path'];
    //

    public function tests()
    {
        return $this->hasMany(Test::class);
    }

}
