<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'content',
        'attachments',
        'pdf_path'
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
