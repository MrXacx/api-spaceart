<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enumerate\Account;
use App\Exceptions\Contracts\HttpRequestException;
use App\Exceptions\NotSavedModelException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Controllers\Contracts\IMainRouteController;
use App\Http\Requests\ArtistRequest;
use App\Http\Requests\EnterpriseRequest;
use App\Repositories\UserRepository;
use App\Services\ResponseService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ControllerMiddlewareOptions;
use Laravel\Sanctum\PersonalAccessToken;
use OpenApi\Annotations as OA;
use Throwable;

class UserController extends IMainRouteController
{
    use AuthorizesRequests;

    public function __construct(ResponseService $responseService,
        private readonly UserRepository $userRepository
    ) {
        parent::__construct($responseService);
    }

    protected function setSanctumMiddleware(): ControllerMiddlewareOptions
    {
        return parent::setSanctumMiddleware()->except('index', 'show', 'store');
    }

    /**
     * @throws HttpRequestException
     * @throws BindingResolutionException
     */
    private function suitRequest(Request $request): FormRequest
    {
        return match (Account::tryFrom((string) $request->type)) { // Find correct request for account type
            Account::ARTIST => app()->make(ArtistRequest::class, $request->all()),
            Account::ENTERPRISE => app()->make(EnterpriseRequest::class, $request->all()),
            default => UnprocessableEntityException::throw('Account type not found'),
        };
    }

    /**
     * @OA\Get(
     *     tags={"/user"},
     *     path="/user",
     *     summary="List active users",
     *     description="Get users on database and paginate them",
     *
     *     @OA\Parameter(
     *      name="limit",
     *      in="query",
     *      description="Limit per page",
     *
     *      @OA\Schema(type="integer"),
     *      style="form"
     *     ),
     *
     *     @OA\Parameter(
     *      name="offset",
     *      in="query",
     *      description="Offset for search",
     *
     *      @OA\Schema(type="integer"),
     *      style="form"
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Users found",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", default="Users found"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
     *             @OA\Property(property="fails", type="bool", default="false"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response="500",
     *          description="Unexpected error",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="message", type="string", default="Unexpected error"),
     *              @OA\Property(property="fails", type="bool", default="true"),
     *          )
     *      ),
     *
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => ['numeric', 'min:1', 'max:50', 'nullable'],
            'offset' => ['numeric', 'min:1', 'nullable'],
        ]);

        return $this->responseService->sendMessage(
            'Users found',
            $this->userRepository->list($request->offset ?? 0, $request->limit ?? 15)->toArray()
        );
    }

    /**
     * @OA\Get(
     *     tags={"/user"},
     *     path="/user/{id}",
     *     summary="Show user",
     *     description="Get an unique user on database",
     *
     *     @OA\Parameter(
     *      name="id",
     *      in="path",
     *      description="User id",
     *
     *      @OA\Schema(type="integer"),
     *      style="form"
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="User found",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", default="User {id} found"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/User"),
     *             @OA\Property(property="fails", type="bool", default="false"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="422",
     *         description="User not found",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", default="User {id} not found"),
     *             @OA\Property(property="errors", type="array", @OA\Items()),
     *             @OA\Property(property="fails", type="bool", default="true"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response="500",
     *          description="Unexpected error",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="message", type="string", default="Unexpected error"),
     *              @OA\Property(property="fails", type="bool", default="true"),
     *          )
     *      ),
     *
     * )
     *
     * @throws HttpRequestException
     */
    public function show(Request $request): JsonResponse
    {
        if ($request->bearerToken()) { // If bearer token exists
            $token = PersonalAccessToken::findToken($request->bearerToken());
            if ($token) {
                auth()->setUser($token->tokenable()->first()); // set token owner to auth
            }
        }
        $user = $this->userRepository->fetch($request->id);

        return $this->responseService->sendMessage("User $request->id found", $user->toArray());
    }

    /**
     * @OA\Post(
     *     path="/user",
     *     tags={"/user"},
     *
     *     @OA\Response(
     *         response="200",
     *          description="User created",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="message", type="string", default="User created"),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
     *              @OA\Property(property="fails", type="bool", default="false")
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response="422",
     *           description="User not created",
     *
     *           @OA\JsonContent(
     *               type="object",
     *
     *               @OA\Property(property="message", type="string", default="User not created"),
     *               @OA\Property(property="errors", type="array", @OA\Items()),
     *               @OA\Property(property="fails", type="bool", default="trur")
     *           )
     *      )
     * )
     *
     * @throws BindingResolutionException
     * @throws HttpRequestException
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $request = $this->suitRequest($request);
        try {
            $user = $this->userRepository->create($request->validated());

            return $this->responseService->sendMessage('User was created', $user->toArray(), 201);
        } catch (NotSavedModelException) {
            return $this->responseService->sendMessage('User was not created');
        }
    }

    public function update(Request $request): JsonResponse
    {
        $request = $this->suitRequest($request);

        try {
            $user = $this->userRepository->update(
                $request->id,
                $request->validated(),
                fn ($u) => $this->authorize('isAdmin', $u)
            );

            return $this->responseService->sendMessage("User $request->id was updated", $user->toArray());
        } catch (NotSavedModelException $e) {
            return $this->responseService->sendError("User $request->id was not updated", [$e->getMessage()]);
        }
    }

    /**
     * @OA\Delete(
     *     tags={"/user"},
     *     path="/user/{id}",
     *     summary="Disable user account",
     *     description="Disable access to user account",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *      name="id",
     *      in="path",
     *      description="User id",
     *
     *      @OA\Schema(type="integer"),
     *      style="form"
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="User disabled",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", default="User disabled"),
     *             @OA\Property(property="fails", type="bool", default="false"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="422",
     *         description="User not found",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", default="User {id} not found"),
     *             @OA\Property(property="errors", type="object"),
     *             @OA\Property(property="fails", type="bool", default="true"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response="500",
     *          description="Unexpected error",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="message", type="string", default="Unexpected error"),
     *              @OA\Property(property="fails", type="bool", default="true"),
     *          )
     *      )
     * )
     */
    public function destroy(Request $request): JsonResponse
    {
        return $this->userRepository->delete(
            $request->id,
            fn ($u) => $this->authorize('isAdmin', $u)
        ) ?
            $this->responseService->sendMessage("Account $request->id was disabled", status: 204) :
            $this->responseService->sendError("User $request->id was not disabled");
    }
}
