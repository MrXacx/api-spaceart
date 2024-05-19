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
use Enumerate\Account;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ControllerMiddlewareOptions;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

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
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return $this->responseService->sendMessage(
            'Users found',
            User::withAllRelations()
                ->where('active', true)
                ->get()
                ->toArray()
        );
    }

    /**
     * @throws NotFoundRecordException
     */
    protected function fetch(string $id): User
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

    public function show(Request $request): JsonResponse
    {
        if ($request->bearerToken()) { // If bearer token exists
            $token = PersonalAccessToken::findToken($request->bearerToken());
            if($token) {
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
