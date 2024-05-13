<?php

namespace App\Policies;

use App\Models\Agreement;
use App\Models\User;

class AgreementPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function isStakeholder(User $auth, Agreement $agreement): bool
    {
        return ($auth->id == $agreement->artist_id) || $this->isEnterprise($auth, $agreement);
    }

    public function isEnterprise(User $auth, Agreement $agreement): bool
    {
        return $auth->id == $agreement->enterprise_id;
    }
}
