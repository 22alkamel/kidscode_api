<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionStudent extends Model
{
    //
        protected $table = 'sessionstudents'; 
        
    protected $fillable = [
        'session_id',
        'student_id',
        'watched_at',
        'submitted_at',
        'score'
    ];

    public function student()
    {
        return $this->belongsTo(User::class);
    }

    public function classsession()
    {
          return $this->belongsTo(ClassSession::class, 'session_id');
    }

}
