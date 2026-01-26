<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student_answers extends Model
{
    //
    protected $fillable = [
        'student_id',
        'question_id',
        'answer',
        'is_correct'
    ];
}
