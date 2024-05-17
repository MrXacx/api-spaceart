<?php

namespace App\Policies;

use App\Models\Selective;
use App\Models\SelectiveCandidate;
use App\Models\User;

class SelectivePolicy
{
    public function isOwner(User $user, Selective $selective): bool
    {
        return $user->id === $selective->enterprise->id;
    }

    public function isCandidate(User $user, SelectiveCandidate $selectiveCandidate): bool
    {
        return $user->id === $selectiveCandidate->artist->id;
    }
}
