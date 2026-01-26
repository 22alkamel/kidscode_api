<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'lesson_id',
        'title',
        'description',
        'submission_type',
        'max_score',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // ينتمي لبرنامج
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // ممكن يكون تابع لدرس معين
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
