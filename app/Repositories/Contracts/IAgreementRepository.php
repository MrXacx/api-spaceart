<?php

namespace App\Repositories\Contracts;

use App\Exceptions\NotFoundException;
use App\Exceptions\NotSavedModelException;
use App\Models\Agreement;
use Closure;
use Illuminate\Database\Eloquent\Collection;

interface IAgreementRepository
{
    public function list(string|int $userID, int $limit): Collection|array;

    /**
     * @throws NotFoundException
     */
    public function fetch(string|int $id, Closure $validate): Agreement;

    /**
     * @throws NotSavedModelException
     */
    public function create(array $data, Closure $validate): Agreement;

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws NotSavedModelException
     */
    public function update(string|int $id, array $data, Closure $validate): Agreement;

    /**
     * @throws NotSavedModelException
     */
    public function delete(string|int $id, Closure $validate): bool;
}
