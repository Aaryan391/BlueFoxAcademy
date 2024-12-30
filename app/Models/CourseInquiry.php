<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'full_name',
        'email',
        'message',
        'status'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
