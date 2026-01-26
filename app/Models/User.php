<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'status',
        'google_id',
        'verification_token',
        'otp_code',
        'otpexpiresat',
        'otp_verified'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified' => 'boolean',
        'otp_verified' => 'boolean',
        'otpexpiresat' => 'datetime',
        'emailverifiedat' => 'datetime'
    ];

    protected $guard_name = 'api';

    // علاقة بروفايل المدرب
    public function trainerProfile()
    {
        return $this->hasOne(TrainerProfile::class);
    }

    // علاقة بروفايل الطالب
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function groups()
    {
    return $this->belongsToMany(
        ProgramGroup::class,
        'group_students',
        'student_id',
        'group_id'
    );
    }

    // التسجيلات في البرامج
    public function enrollments()
    {
        return $this->hasMany(ProgramEnrollment::class);
    }
    public function registrations()
{
    return $this->hasMany(Registration::class);
}


    // البرامج التي تم إنشاؤها بواسطة المستخدم (المدرب/الأدمن)
    public function createdPrograms()
    {
        return $this->hasMany(Program::class, 'created_by');
    }
}
