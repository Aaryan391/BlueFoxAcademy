<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseFeedback extends Model
{
    protected $fillable = [
        'course_id', 'student_id', 'rating', 'comment'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
