<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model

{
    
    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'level',
        'agemin',
        'agemax',
        'duration_weeks',
        'price',
        'created_by',
        'is_published'
    ];

    
    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function enrollments()
    {
        return $this->hasMany(ProgramEnrollment::class);
    }

     public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    // المشاريع أو الاختبارات النهائية
    public function examsProjects()
    {
        return $this->hasMany(ExamProject::class);
    }

    public function getRouteKeyName()
{
    return 'slug';
}

    
}
