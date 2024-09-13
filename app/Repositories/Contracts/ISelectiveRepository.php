<?php

namespace App\Repositories\Contracts;

use App\Exceptions\NotSavedModelException;
use App\Models\Selective;
use Closure;
use Illuminate\Database\Eloquent\Collection;

interface ISelectiveRepository
{
    public function list(int|string $offset, int|string $limit): Collection|array;

    public function fetch(int|string $id): Selective;

    /**
     * @throws NotSavedModelException
     */
    public function create(array $data, Closure $validate): Selective;

    /**
     * @throws NotSavedModelException
     */
    public function update(int|string $id, array $data, Closure $validate): Selective;

    public function delete(int|string $id, Closure $validate): bool;
}
