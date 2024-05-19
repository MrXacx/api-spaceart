<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Checa se o usuário passado por requisição é propietário do token
     */
    public function isAdmin(User $authUser, User $requestUser): bool
    {
        return $authUser->id === $requestUser->id;
    }
}
