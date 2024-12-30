<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'duration',
        'skill_level',
        'language',
        'num_students',
        'schedule',
        'type',
        'has_assessments',
        'status',
        'course_image',
    ];
    protected $casts = [
        'schedule' => 'array'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(CourseFeedback::class);
    }
    public function getScheduleAttribute($value)
    {
        return json_decode($value, true);
    }
    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }
}
