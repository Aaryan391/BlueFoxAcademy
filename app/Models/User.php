<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'role',
        'address', 'city', 'state', 'postcode',
        'profile_picture', 'bio', 'occupation',
        'company_name', 'linkedin', 'facebook',
        'twitter', 'instagram','teacher_request_status',
        'teacher_expertise',
        'teacher_request_submitted_at',
        'teacher_request_processed_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'teacher_request_submitted_at' => 'datetime',
            'teacher_request_processed_at' => 'datetime'
        ];
    }
    public function courses()
    {
        return $this->hasMany(Course::class, 'user_id');
    }

    public function enrollments() {
        return $this->hasMany(Enrollment::class);
    }
    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }
    /**
     * Check if the user has a specific role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if the user has any of the given roles
     *
     * @param array|string $roles
     * @return bool
     */
    public function hasAnyRole($roles)
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }

        return in_array($this->role, $roles);
    }

    /**
     * Get the user's role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Check if user is an admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a teacher
     *
     * @return bool
     */
    public function isTeacher()
    {
        return $this->hasRole('teacher');
    }

    /**
     * Check if user is a regular user
     *
     * @return bool
     */
    public function isUser()
    {
        return $this->hasRole('user');
    }
}
