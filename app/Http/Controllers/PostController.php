<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundRecordException;
use App\Exceptions\NotSavedModelException;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ControllerMiddlewareOptions;

class PostController extends IMainRouteController
{
    protected function setSanctumMiddleware(): ControllerMiddlewareOptions
    {
        return parent::setSanctumMiddleware()->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate(['limit' => ['numeric', 'min:1', 'max:50']]);

        return $this->responseService->sendMessage(
            'Posts found.',
            Post::withAllRelations()
                ->limit($request->limit ?? 10)
                ->inRandomOrder()
                ->get()
                ->filter(fn ($p) => $p->user->active)
                ->toArray()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request): JsonResponse
    {
        $post = new Post($request->validated());
        $this->authorize('isAdmin', $post->user);

        try {
            throw_unless($post->save(), NotSavedModelException::class);

            return $this->responseService->sendMessage('Post created.', $post->toArray());
        } catch (NotSavedModelException $e) {
            return $this->responseService->sendError('Post not created.', [$e->getMessage()]);
        }
    }

    public function fetch(string|int $id): Post
    {
        return Post::findOr($id, fn () => NotFoundRecordException::throw("Post $id not found."))->loadAllRelations();
    }

    /**
     * Display the specified resource.
     */
    public function show(PostRequest $request): JsonResponse
    {
        return $this->responseService->sendMessage('Post found.', [$this->fetch($request->post)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostRequest $request): JsonResponse
    {
        $post = $this->fetch($request->post);
        $this->authorize('isOwner', $post);

        return $post->delete() ?
            $this->responseService->sendMessage('Post deleted.') :
            $this->responseService->sendError('Post not deleted.');
    }
}
