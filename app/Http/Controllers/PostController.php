<?php

namespace App\Http\Controllers;

use App\Exceptions\NotSavedModelException;
use App\Http\Requests\PostRequest;
use App\Repositories\PostRepository;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ControllerMiddlewareOptions;

class PostController extends IMainRouteController
{
    public function __construct(
        ResponseService $responseService,
        private readonly PostRepository $postRepository,
    ) {
        parent::__construct($responseService);
    }

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
            $this->postRepository->list($request->limit ?? 50)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request): JsonResponse
    {
        try {
            $post = $this->postRepository->create(
                $request->validated(),
                fn ($p) => $this->authorize('isAdmin', $p->user)
            );

            return $this->responseService->sendMessage('Post created.', $post->toArray(), 201);
        } catch (NotSavedModelException) {
            return $this->responseService->sendError('Post not created.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PostRequest $request): JsonResponse
    {
        return $this->responseService->sendMessage("Post $request->post was found.", $this->postRepository->fetch($request->post));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostRequest $request): JsonResponse
    {
        return $this->postRepository->delete(
            $request->post,
            fn ($p) => $this->authorize('isAdmin', $p->user)
        ) ?
            $this->responseService->sendMessage('Post deleted.') :
            $this->responseService->sendError('Post not deleted.');
    }
}
