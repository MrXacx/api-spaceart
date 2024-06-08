<?php

namespace App\Http\Controllers;

use App\Exceptions\NotSavedModelException;
use App\Http\Controllers\Contracts\IMainRouteController;
use App\Http\Requests\PostRequest;
use App\Repositories\PostRepository;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ControllerMiddlewareOptions;
use OpenApi\Annotations as OA;

/**
 * @OA\Response(
 *          response="ReturnPost",
 *          description="Operation finished succesfully",
 *
 *          @OA\JsonContent(
 *
 *              @OA\Property(property="message", type="string", default="Post was created"),
 *              @OA\Property(property="data", type="array", @OA\Items(maxItems=1, ref="#/components/schemas/Post")),
 *              @OA\Property(property="fails", type="boolean", default="false"),
 *          )
 *      ),
 */
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
     *
     * @OA\Get(
     *     tags={"Post"},
     *     path="/post",
     *     summary="List random posts",
     *     description="Fetch random posts on database",
     *
     *     @OA\Parameter(ref="#/components/parameters/Limit"),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Posts were found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Post")),
     *             @OA\Property(property="fails", type="boolean", default="false"),
     *         )
     *     ),
     *
     * )
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
     *
     * @OA\Post(
     *     tags={"Post"},
     *     path="/post",
     *     summary="Publish post",
     *     description="Store post",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\RequestBody(ref="#/components/requestBodies/PostStore"),
     *
     *     @OA\Response(response="201", ref="#/components/responses/ReturnPost"),
     * )
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
     *
     * @OA\Get(
     *     tags={"Post"},
     *     path="/post/{id}",
     *     summary="Fetch unique post",
     *     description="Fetch post on database",
     *
     *     @OA\Parameter(ref="#/components/parameters/Id"),
     *
     *     @OA\Response(response="200", ref="#/components/responses/ReturnPost"),
     * )
     */
    public function show(PostRequest $request): JsonResponse
    {
        return $this->responseService->sendMessage("Post $request->post was found.", $this->postRepository->fetch($request->post));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Post(
     *      tags={"Post"},
     *      path="/post/{id}/delete",
     *      summary="[DELETE]::/post/{id} alias",
     *      description="Redirect request to [DELETE]::/post/{id}",
     *
     *      @OA\Response(response="302", description="Redirected to [DELETE]::/post/{id}"),
     * )
     *
     * @OA\Delete(
     *     tags={"Post"},
     *     path="/post/{id}",
     *     summary="Delete post",
     *     description="Delete post on database",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\Parameter(ref="#/components/parameters/Id"),
     *
     *     @OA\Response(
     *         response="204",
     *         description="Post deleted",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", default="Post was deleted"),
     *             @OA\Property(property="fails", type="boolean", default="false"),
     *         )
     *     ),
     * )
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
