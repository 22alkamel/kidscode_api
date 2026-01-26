<?php

namespace App\Policies;

use App\Models\Track;
use App\Models\User;

class TrackPolicy
{
    public function access(User $user, Track $track)
    {
        if ($user->role === 'admin') return true;

        if ($user->role === 'trainer' && $track->program->trainer_id === $user->id) {
            return true;
        }

        return false;
    }
}
