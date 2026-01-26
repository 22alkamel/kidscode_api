<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramEnrollment extends Model
{
    protected $fillable = [
        'program_id',
        'user_id',
        'age_group',
        'enrolled_at',
        'payment_status',
        'payment_method',
        'payment_reference',
        'confirmation_note',
        'confirmed_by',
        'confirmed_at',
        'activated',
        'activation_at'
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'activation_at' => 'datetime'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
