<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    //
    protected $table = 'student_answers';
    
    protected $fillable = [
        'student_id',
        'question_id',
        'answer',
        'is_correct'
    ];
}
