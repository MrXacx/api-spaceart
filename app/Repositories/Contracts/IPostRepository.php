<?php

namespace App\Repositories\Contracts;

use App\Exceptions\NotSavedModelException;
use App\Models\Post;
use Ramsey\Collection\Collection;

interface IPostRepository
{
    public function list(int $limit): Collection|array;

    public function fetch(string|int $id): Post;

    /**
     * @throws NotSavedModelException
     */
    public function create(array $data, \Closure $validate): Post;

    public function delete(string|int $id, \Closure $validate): bool;
}
