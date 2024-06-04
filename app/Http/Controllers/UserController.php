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

/**
 * @OA\Response(
 *  response="ReturnUser",
 *   description="Response is successful",
 *
 *   @OA\JsonContent(
 *
 *       @OA\Property(property="message", type="string", default="User was updated"),
 *       @OA\Property(property="data", type="array", maxItems=1, @OA\Items(ref="#/components/schemas/User")),
 *       @OA\Property(property="fails", type="bool")
 *   )
 * ),
 */
class UserController extends IMainRouteController
{
    use AuthorizesRequests;

    public function __construct(
        ResponseService $responseService,
        private readonly UserRepository $userRepository
    ) {
        parent::__construct($responseService);
    }

    protected function setSanctumMiddleware(): ControllerMiddlewareOptions
    {
        return parent::setSanctumMiddleware()->except('index', 'show', 'store');
    }

    /**
     * Get the correct FormRequest to account type
     *
     * @throws HttpRequestException
     * @throws BindingResolutionException
     */
    private function suitRequest(Request $request): FormRequest
    {
        return match (Account::tryFrom((string) $request->type)) { // Build correct request for account type
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
     *     @OA\Parameter(ref="#/components/parameters/Limit"),
     *     @OA\Parameter(ref="#/components/parameters/Offset"),
     *     @OA\Parameter(
     *      name="start_with",
     *      in="query",
     *      description="User's name start with",
     *
     *      @OA\Schema(type="string", nullable=true),
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
     *     @OA\Response(response="500", ref="#/components/responses/500")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => ['numeric', 'min:1', 'max:50', 'nullable'],
            'offset' => ['numeric', 'min:1', 'nullable'],
            'start_with' => ['string', 'nullable'],
        ]);

        return $this->responseService->sendMessage(
            'Users found',
            $this->userRepository->list(
                $request->offset ?? 0,
                $request->limit ?? 15,
                $request->start_with ?? ''
            )->toArray()
        );
    }

    /**
     * @OA\Get(
     *     tags={"/user"},
     *     path="/user/{id}",
     *     summary="Show user",
     *     description="Get an unique user on database",
     *
     *     @OA\Parameter(ref="#/components/parameters/Id"),
     *
     *     @OA\Response(response="200", ref="#/components/responses/ReturnUser"),
     *     @OA\Response(response="422", ref="#/components/responses/422"),
     *     @OA\Response(response="500", ref="#/components/responses/500"),
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
     *     summary="Store user",
     *     description="Store user on database",
     *
     *     @OA\RequestBody(ref="#/components/requestBodies/UserStore"),
     *
     *     @OA\Response(
     *         response="201",
     *          description="User was created",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", default="User created"),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
     *              @OA\Property(property="fails", type="bool")
     *          )
     *     ),
     *
     *     @OA\Response(response="422", ref="#/components/responses/422"),
     *     @OA\Response(response="500", ref="#/components/responses/500")
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

    /**
     * @OA\Post(
     *    path="/user/{id}/update",
     *    summary="[PUT]::/user/{id} alias",
     *    description="Alternative route to [PUT]::/user/{id}",
     *    tags={"/user"},
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *    @OA\Response(response="302", description="Redirected to [PUT]::/user/{id}")
     * )
     *
     * @OA\Put(
     *     path="/user/{id}",
     *     tags={"/user"},
     *     summary="Update user",
     *     description="Update user, artist or enterprise data on database",
     *
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\RequestBody(ref="#/components/requestBodies/UserUpdate"),
     *
     *     @OA\Response(response="200", ref="#/components/responses/ReturnUser"),
     *     @OA\Response(response="401", ref="#/components/responses/401"),
     *     @OA\Response(response="422", ref="#/components/responses/422"),
     *     @OA\Response(response="500", ref="#/components/responses/500"),
     * )
     *
     * @throws BindingResolutionException
     * @throws HttpRequestException
     * @throws Throwable
     */
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
     * @OA\Post(
     *     tags={"/user"},
     *     path="/user/{id}/delete",
     *     summary="[DELETE]::/user/{id} alias",
     *     description="Alternative route to [DELETE]::/user/{id}",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\Response(response="200", description="Redirected to [DELETE]::/user/{id}")
     * )
     *
     * @OA\Delete(
     *     tags={"/user"},
     *     path="/user/{id}",
     *     summary="Disable user account",
     *     description="Disable access to user account",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
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
     *     @OA\Response(response="204", ref="#/components/responses/204"),
     *     @OA\Response(response="401", ref="#/components/responses/401"),
     *     @OA\Response(response="500", ref="#/components/responses/500"),
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
