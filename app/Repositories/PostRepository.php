<?php

namespace App\Repositories;

use App\Exceptions\NotFoundModelException;
use App\Exceptions\NotSavedModelException;
use App\Exceptions\TrashedModelReferenceException;
use App\Models\Post;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PostRepository implements Contracts\IPostRepository
{
    public function list(int|string $limit): Collection|array
    {
        return Post::withAllRelations()
            ->where('created_at', '>=', now()->subDays(2))
            ->whereHas('user', fn (Builder $u) => $u->whereNull('deleted_at'))
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function fetch(int|string $id): Post
    {
        return Post::findOr($id, fn () => NotFoundModelException::throw("Post $id was not found."))->loadAllRelations();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data, Closure $validate): Post
    {
        $post = new Post($data);
        $validate($post);

        throw_if($post->user->trashed(), TrashedModelReferenceException::class, "The user account {$post->user->id} is disabled");
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
