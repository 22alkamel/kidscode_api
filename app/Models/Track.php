<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'title',
        'track_img',
        'slug',
        'description',
        'order',
        'estimated_time',
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

    // ينتمي لبرنامج
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // يحتوي على دروس
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }
    
}
