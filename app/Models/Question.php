<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'type',
        'question',
        'options',
        'correct_answer',
        'order',
    ];

    protected $casts = [
        'options' => 'array', // JSON → array
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationshipsشؤ
    |--------------------------------------------------------------------------
    */

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
