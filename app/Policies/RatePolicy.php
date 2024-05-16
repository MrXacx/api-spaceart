<?php

namespace App\Policies;

use App\Models\Rate;
use App\Models\User;

class RatePolicy
{
    public function isAuthor(User $user, Rate $rate): bool
    {
        return $user->id === $rate->author->id;
    }
}
