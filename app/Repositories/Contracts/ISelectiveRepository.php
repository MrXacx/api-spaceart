<?php

namespace App\Repositories\Contracts;

use App\Exceptions\NotSavedModelException;
use App\Models\Selective;
use Closure;
use Illuminate\Database\Eloquent\Collection;

interface ISelectiveRepository
{
    public function list(int $offset, int $limit): Collection|array;

    public function fetch(string|int $id): Selective;

    /**
     * @throws NotSavedModelException
     */
    public function create(array $data, Closure $validate): Selective;

    /**
     * @throws NotSavedModelException
     */
    public function update(string|int $id, array $data, Closure $validate): Selective;

    public function delete(string|int $id, Closure $validate): bool;
}
