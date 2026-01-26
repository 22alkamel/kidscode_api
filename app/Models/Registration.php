<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class Registration extends Model
{
    //
     use HasFactory;

    protected $fillable = [
        'user_id',
        'program_id',
        'payment_id',
        'preferred_days',
        'preferred_time',
        'status',
    ];

    // خيارات الأيام المسموح بها
    const DAYS_OPTIONS = [
        'sat_tue' => 'السبت و الثلاثاء',
        'sun_wed' => 'الأحد و الأربعاء',
        'mon_thu' => 'الاثنين و الخميس',
    ];

    // خيارات الأوقات المسموح بها
    const TIME_OPTIONS = [
        '08-10' => '8 إلى 10 صباحا',
        '10-12' => '10 إلى 12 صباحا',
        '13-15' => '1 إلى 3 مساء',
        '15-17' => '3 إلى 5 مساء',
        '19-21' => '7 إلى 9 مساء',
    ];

    // علاقات
    public function user() 
    {
         return $this->belongsTo(User::class); 
    }
    
    public function program()
     {
         return $this->belongsTo(Program::class); 
    }

    // public function enrollments()
    // { 
    //     return $this->belongsTo(ProgramEnrollment::class);
    // }

    public function payment()
    {
        return $this->belongsTo(ProgramEnrollment::class, 'payment_id');
    }
    // في موديل Registration
public function groupStudents()
{
    return $this->hasMany(GroupStudent::class, 'student_id', 'user_id');
}



}
