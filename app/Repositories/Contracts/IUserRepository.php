<?php

namespace App\Repositories\Contracts;

use App\Exceptions\NotSavedModelException;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Collection;

interface IUserRepository
{
    public function list(int $offset, int $limit): Collection|array;

    public function fetch(string|int $id): User;

    /**
     * @throws NotSavedModelException
     */
    public function create(array $data): User;

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws NotSavedModelException
     */
    public function update(string|int $id, array $data, Closure $validate): User;

    /**
     * @throws NotSavedModelException
     */
    public function delete(string|int $id, Closure $validate): bool;
}
