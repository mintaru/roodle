<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentFile extends Model
{
    protected $fillable = [
        'assignment_id',
        'title',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
}
