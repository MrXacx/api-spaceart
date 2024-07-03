<?php

namespace App\Repositories\Contracts;

use App\Models\Contracts\UserAuxAccount;
use Closure;

interface IUserAuxAccountRepository
{
    public function create(array $data): UserAuxAccount;

    public function update(int|string $id, array $data, Closure $validate): UserAuxAccount;

    public function delete(int|string $id, Closure $validate): bool;
}
