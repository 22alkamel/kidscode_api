<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramGroup extends Model
{
    //
     protected $fillable = [
        'program_id',
        'name',
        'trainer_id',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'group_students', 'group_id', 'student_id');
    }
    
    public function classSessions()
{
    return $this->hasMany(ClassSession::class, 'group_id');
}

}
