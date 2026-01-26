<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'specialization',
        'experience_years',
        'certifications',
        'phone_number',
        'whatsapp_number'
    ];

    protected $casts = [
        'certifications' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
