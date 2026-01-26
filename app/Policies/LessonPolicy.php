<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;

class LessonPolicy
{
    public function access(User $user, Lesson $lesson)
    {
        if ($user->role === 'admin') return true;

        if ($user->role === 'trainer' && $lesson->track->program->trainer_id === $user->id) {
            return true;
        }

        return false;
    }
}
