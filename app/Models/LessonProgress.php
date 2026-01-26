<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    //
     protected $fillable = ['lesson_id','student_id','status'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
 


// ؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟ظ