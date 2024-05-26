<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\NotFoundRecordException;
use App\Exceptions\NotSavedModelException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Requests\ArtistRequest;
use App\Http\Requests\EnterpriseRequest;
use App\Models\Artist;
use App\Models\Enterprise;
use App\Models\User;
use App\Services\Clients\PostalCodeClientService;
use App\Enumerate\Account;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ControllerMiddlewareOptions;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use OpenApi\Annotations as OA;

class UserController extends IMainRouteController
{
    use AuthorizesRequests;

    protected function setSanctumMiddleware(): ControllerMiddlewareOptions
    {
        return parent::setSanctumMiddleware()->except('index', 'show', 'store');
    }

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
     *              @OA\Property(property="message", type="string", default="Internal error! Please, report it on https://github.com/MrXacx/api-spaceart/issues/new/"),
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
            User::withAllRelations()
                ->where('active', true)
                ->offset($request->offset ?? 0)
                ->limit($request->limit ?? 15)
                ->get()
                ->toArray()
        );
    }

    /**
     * @throws NotFoundRecordException
     */
    protected function fetch(string|int $id): Model
    {
        $user = User::findOr(
            $id,
            fn () => NotFoundRecordException::throw("User $id was not found")
        )// Fetch by PK
            ->loadAllRelations();

        throw_unless(
            $user->active, // Unless account is active
            new NotFoundRecordException("User $id's account is disabled")
        );

        if (auth()->user()?->id == $id) {
            $user->showConfidentialData();
        }

        return $user;
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
     *              @OA\Property(property="message", type="string", default="Internal error! Please, report it on https://github.com/MrXacx/api-spaceart/issues/new/"),
     *              @OA\Property(property="fails", type="bool", default="true"),
     *          )
     *      ),
     *
     * )
     */
    public function show(Request $request): JsonResponse
    {
        if ($request->bearerToken()) { // If bearer token exists
            $token = PersonalAccessToken::findToken($request->bearerToken());
            if ($token) {
                auth()->setUser($token->tokenable()->first()); // set token owner to auth
            }
        }
        $user = $this->fetch($request->id);

        return $this->responseService->sendMessage("User $request->id found", $user->toArray());
    }

    public function store(Request $request): JsonResponse
    {
        $request = $this->suitRequest($request);

        $addressData = PostalCodeClientService::make()->get($request->postal_code); // Fetch city and state
        $requestParameters = $request->all() + (array) $addressData->getData(); // Merge request data and zip code API response

        $user = new User($requestParameters); // Build user
        $typedAccountData = $request->type == Account::ARTIST ? new Artist : new Enterprise;
        $typedAccountData->fill($requestParameters);

        DB::beginTransaction();
        try {
            throw_unless($user->save(), NotSavedModelException::class);
            $typedAccountData->id = $user->id;
            throw_unless($typedAccountData->save(), NotSavedModelException::class);
            DB::commit();

            return $this->responseService->sendMessage('User was created', $user->loadAllRelations()->toArray());
        } catch (Exception $e) {
            DB::rollBack();

            return $this->responseService->sendError('User not created', [$e->getMessage()]);
        }
    }

    public function update(Request $request): JsonResponse
    {
        $request = $this->suitRequest($request);
        $userData = $request->validated(); // Get all validated data

        if ($request->exists('postal_code')) { // Fetch information derived from the zip code
            $userData += (array) PostalCodeClientService::make()->get($request->postal_code)->getData();
        }

        $user = $this->fetch($request->id); // Fetch user

        $this->authorize('isAdmin', $user);
        $user->fill($userData);

        $accountData = $user->artistAccountData ?? $user->enterpriseAccountData;
        $accountData->fill($userData);

        DB::beginTransaction();
        try {
            throw_unless($user->save() && $accountData->save(), NotSavedModelException::class);
            DB::commit();

            return $this->responseService->sendMessage("User $user->id was updated", $user->toArray());
        } catch (Exception $e) {
            DB::rollBack();

            return $this->responseService->sendError("User $user->id was not updated", [$e->getMessage()]);
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
     *              @OA\Property(property="message", type="string", default="Internal error! Please, report it on https://github.com/MrXacx/api-spaceart/issues/new/"),
     *              @OA\Property(property="fails", type="bool", default="true"),
     *          )
     *      )
     * )
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = User::find($request->id);
        $this->authorize('isAdmin', $user);
        $user->fill([
            'image' => null,
            'slug' => null,
            'active' => false,
        ]);

        return $user->save() ?
            $this->responseService->sendMessage("Account $request->id was disabled") :
            $this->responseService->sendError("User $request->id was not disabled");
    }
}
