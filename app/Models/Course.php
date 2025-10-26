<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['title', 'description', 'image_path', 'user_id'];
    //

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

}
