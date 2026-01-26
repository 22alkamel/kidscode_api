<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupStudent extends Model
{
    //
    
    protected $fillable = [
        'group_id',
        'student_id',
    ];

    public function group()
    {
        return $this->belongsTo(ProgramGroup::class, 'group_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    
}
