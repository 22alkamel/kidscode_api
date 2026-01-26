<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    //
    protected $table="session_state";
        protected $fillable = [
        'group_id',
        'lesson_id',
        // 'video_url',
        'publish_at',
        'is_active'
    ];

    public function group()
    {
        return $this->belongsTo(ProgramGroup::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
    

    public function students()
    {
        return $this->hasMany(SessionStudent::class);
    }

}
