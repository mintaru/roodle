<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentSubmissionFile extends Model
{
    protected $fillable = [
        'assignment_submission_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    public function submission()
    {
        return $this->belongsTo(AssignmentSubmission::class, 'assignment_submission_id');
    }
}
