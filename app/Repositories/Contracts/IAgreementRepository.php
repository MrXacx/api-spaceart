<?php

namespace App\Repositories\Contracts;

use App\Exceptions\NotFoundModelException;
use App\Exceptions\NotSavedModelException;
use App\Models\Agreement;
use Closure;
use Illuminate\Database\Eloquent\Collection;

interface IAgreementRepository
{
    public function list(int|string $userID, int|string $limit): Collection|array;

    /**
     * @throws NotFoundModelException
     */
    public function fetch(int|string $id, Closure $validate): Agreement;

    /**
     * @throws NotSavedModelException
     */
    public function create(array $data, Closure $validate): Agreement;

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws NotSavedModelException
     */
    public function update(int|string $id, array $data, Closure $validate): Agreement;

    /**
     * @throws NotSavedModelException
     */
    public function delete(int|string $id, Closure $validate): bool;
}
