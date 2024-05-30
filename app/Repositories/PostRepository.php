<?php

namespace App\Repositories;

use App\Exceptions\NotFoundException;
use App\Exceptions\NotSavedModelException;
use App\Models\Post;
use Closure;
use Ramsey\Collection\Collection;

class PostRepository implements Contracts\IPostRepository
{
    public function list(int $limit): Collection|array
    {
        return Post::withAllRelations()
            ->where(fn ($p) => $p->user->active)
            ->limit($limit)
            ->inRandomOrder()
            ->get();
    }

    public function fetch(int|string $id): Post
    {
        return Post::findOr($id, fn () => NotFoundException::throw("Post $id was not found."))->loadAllRelations();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data, Closure $validate): Post
    {
        $post = new Post($data);
        $validate($post->user);

        throw_unless($post->user->active, NotSavedModelException::class);

        throw_unless($post->save(), NotSavedModelException::class);

        return $post->loadAllRelations();
    }

    public function delete(int|string $id, Closure $validate): bool
    {
        $post = $this->fetch($id);
        $validate($post);

        return $post->delete();
    }
}
