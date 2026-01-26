<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'type',
        'url',
        'caption',
        'order',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
