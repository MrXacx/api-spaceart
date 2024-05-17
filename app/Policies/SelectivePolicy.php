<?php

namespace App\Policies;

use App\Models\Selective;
use App\Models\User;

class SelectivePolicy
{
    /**
     * Create a new policy instance.
     */
    public function isOwner(User $user, Selective $selective): bool {
        return $user->id === $selective->enterprise->id;
    }
}
