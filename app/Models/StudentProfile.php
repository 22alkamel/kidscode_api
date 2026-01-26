<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model

{
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'age',
        'school',
        'grade',
        'guardian_name',
        'guardian_phone',
        'interests'
    ];

    protected $casts = [
        'interests' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
