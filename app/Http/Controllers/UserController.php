<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Artist;
use Enumerate\Account;
use App\Models\Enterprise;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\ArtistRequest;
use App\Exceptions\NotFoundRecordException;
use App\Http\Requests\EnterpriseRequest;
use App\Exceptions\UnprocessableEntityException;
use App\Services\Clients\PostalCodeClientService;

class UserController extends IController
{
    private function suitRequestRecursive(Request $request): void
    {
        $request = match (Account::tryFrom((string) $request->type)) { // Find correct request for account type
            Account::ARTIST => ArtistRequest::createFrom($request),
            Account::ENTERPRISE => EnterpriseRequest::createFrom($request),
            default => UnprocessableEntityException::throw('Account type not found'),
        };
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse|RedirectResponse
    {
        return $this->responseService->sendMessage(
            "Lista de usuário encontrada",
            User::all()
                ->filter(fn($u) => $u->active)
                ->toArray()
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

        if (!($user?->active XOR $user?->user?->active)) { // If $user is null or account is deactivate
            NotFoundRecordException::throw("User $id not found");
        }

        dd(auth()->user());

        $user->makeVisibleIf(auth()->user()?->id === $id, ['phone', 'cnpj', 'cpf']);

        return $user;
    }

    public function show(Request $request): JsonResponse|RedirectResponse
    {

        $user = $this->fetch($request->user, ['type' => $request->type]);

        $message = Session::get('message', 'Search finished without errors');
        return $this->responseService->sendMessage($message, $user);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->suitRequestRecursive($request);

        /*$errors = $request->validate();

        if ($errors) {
            return $this->responseService->sendError("Falha na validação", $errors);
        }*/

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
        $this->suitRequestRecursive($request);

        /*if ($errors = $request->validate()) {
             return $this->responseService->sendError("Validate failed", $errors);
         }*/

        $account = $this->fetch(auth()->user()->id, [
            'type' => auth()->user()->type
        ]);

        $addressData = $request->exists('postal_code') ? (array) PostalCodeClientService::make()->get($request->postal_code)->getData() : [];

        $userData = array_filter(
            array_merge(
                $request->all(),
                $addressData
            ),
            fn($item) => !is_null($item),
        );

        $account->fill($userData);
        $account->user->fill($userData);


        DB::transaction(function () use ($account) {
            $account->save();
            $account->user->save();
        });

        return redirect()->route('user.show', $account->user->only('id', 'type'))->with('message', "User $account->id was updated");
    }

    public function destroy(Request $request): JsonResponse|RedirectResponse
    {
        $user = User::find(auth()->user()->id);
        $user->active = false;

        return $user->save() ?
            $this->responseService->sendMessage("Account $request->id disabled") :
            $this->responseService->sendError("User $request->id not disabled");
    }
}
