<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\NotFoundRecordException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Requests\ArtistRequest;
use App\Http\Requests\EnterpriseRequest;
use App\Models\Artist;
use App\Models\Enterprise;
use App\Models\User;
use App\Services\Clients\PostalCodeClientService;
use Enumerate\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserController extends IController
{
    use AuthorizesRequests;

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
    public function index(): JsonResponse|RedirectResponse
    {
        return $this->responseService->sendMessage(
            'Lista de usuário encontrada',
            User::where('active', true)->get()->toArray()
        );
    }

    protected function fetch(string $id, array $options = []): Model
    {
        $user = match (Account::tryFrom((string) $options['type'])) {
            Account::ARTIST => new Artist,
            Account::ENTERPRISE => new Enterprise,
            default => new User
        };

        $user = $user::find($id); // Fetch by PK

        if (! ($user?->active xor $user?->user?->active)) { // If $user is null or account is deactivate
            NotFoundRecordException::throw("User $id not found");
        }

        $user->makeVisibleIf(auth()->user()?->id === $id, ['phone', 'cnpj', 'cpf']);

        return $user;
    }

    public function show(Request $request): JsonResponse|RedirectResponse
    {
        $user = $this->fetch($request->id, ['type' => $request->type]);
        $message = Session::get('message', 'Search finished without errors');

        return $this->responseService->sendMessage($message, $user);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request = $this->suitRequest($request);

        $addressData = PostalCodeClientService::make()->get($request->postal_code); // Fetch city and state

        $requestParameters = array_merge( // Merge request data and zip code API response
            $request->all(),
            (array) $addressData->getData()
        );

        $user = new User($requestParameters); // Build user

        DB::transaction(function () use ($requestParameters, $user) {
            $user->save(); // Insert on table

            $account = $user->type == Account::ARTIST ? new Artist : new Enterprise;
            $account->fill($requestParameters); // Fill artist or enterprise
            $account->id = $user->id;
            $account->save();
        });

        return redirect()->route(
            'user.show',
            $user->id
        )->with('message', "User $user->id was created");
    }

    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $request = $this->suitRequest($request);
        $userData = $request->validated(); // Get all validated data

        if ($request->exists('postal_code')) { // Fetch information derived from the zip code
            $userData = $userData + (array) PostalCodeClientService::make()->get($request->postal_code)->getData();
        }

        $account = $this->fetch( // Fetch user
            (string) auth()->user()->id,
            [
                'type' => auth()->user()->type,
            ]
        );

        $account->fill($userData); // Fill artist/enterprise
        $account->user->fill($userData); // Fill general user

        DB::transaction(function () use ($account) {
            $account->save();
            $account->user->save();
        });

        return redirect()->route('user.show', $account->user->only('id', 'type'))->with('message', "User $account->id was updated");
    }

    public function destroy(Request $request): JsonResponse|RedirectResponse
    {
        $user = User::find(auth()->user()->id);
        $user->fill([
            'image' => null,
            'slug' => null,
            'active' => false,
        ]);
        $user->active = false;

        return $user->save() ?
            $this->responseService->sendMessage("Account $request->id disabled") :
            $this->responseService->sendError("User $request->id not disabled");
    }
}
