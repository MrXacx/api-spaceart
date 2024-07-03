<?php

namespace App\Repositories\Contracts;

use App\Exceptions\NotSavedModelException;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

interface IPostRepository
{
    public function list(int|string $limit): Collection|array;

    public function fetch(int|string $id): Post;

    /**
     * @throws NotSavedModelException
     */
    public function create(array $data, \Closure $validate): Post;

    public function delete(int|string $id, \Closure $validate): bool;
}
