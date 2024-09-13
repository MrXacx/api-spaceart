<?php

namespace App\Repositories\Contracts;

use App\Exceptions\NotSavedModelException;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Collection;

interface IUserRepository
{
    public function list(int|string $offset, int|string $limit, string $startWith = ''): Collection|array;

    public function fetch(int|string $id): User;

    /**
     * @throws NotSavedModelException
     */
    public function create(array $data): User;

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws NotSavedModelException
     */
    public function update(int|string $id, array $data, Closure $validate): User;

    /**
     * @throws NotSavedModelException
     */
    public function delete(int|string $id, Closure $validate): bool;
}
