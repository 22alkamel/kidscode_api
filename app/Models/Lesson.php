<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'track_id',
        'title',
        'slug',
        'content',
        'order',
        'duration_minutes',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // ينتمي لتراك
    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    // الوسائط الخاصة بالدرس
    public function media()
    {
        return $this->hasMany(LessonMedia::class)->orderBy('order');
    }

    // الأسئلة الخاصة بالدرس
    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    // مشاريع مرتبطة بدرس ما
    public function examsProjects()
    {
        return $this->hasMany(ExamProject::class);
    }

public function classSessions()
{
    return $this->hasMany(ClassSession::class);
}


}
