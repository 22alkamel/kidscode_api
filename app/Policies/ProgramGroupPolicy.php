<?php

namespace App\Policies;

use App\Models\ProgramGroup;
use App\Models\User;

class ProgramGroupPolicy
{
    public function access(User $user, ProgramGroup $group)
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'trainer' && $group->trainer_id === $user->id) {
            return true;
        }

        return false;
    }
}
