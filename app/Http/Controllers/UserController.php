<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\NotFoundRecordException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Requests\User\ArtistRequest;
use App\Http\Requests\User\EnterpriseRequest;
use App\Models\Artist;
use App\Models\Enterprise;
use App\Models\User;
use App\Models\ViewModels\ArtistUserView;
use App\Models\ViewModels\EnterpriseUserView;
use App\Services\Clients\PostalCodeClientService;
use App\Services\ResponseService;
use Enumerate\Account;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends IController
{
    private function handleFormRequest(FormRequest $request): ArtistRequest|EnterpriseRequest
    {
        return match (Account::tryFrom((string) $request->type)) { // Find correct request for account type
            Account::ARTIST => ArtistRequest::createFrom($request),
            Account::ENTERPRISE => EnterpriseRequest::createFrom($request),
            default => UnprocessableEntityException::throw('Account type not found.'),
        };
    }

    /**
     * Display a listing of the resource.
     */
    public function list(): Collection|JsonResponse
    {
        return $this->responseService->sendMessage("Lista de usuário encontrada", User::all()->toArray());
    }

    public function store(FormRequest $request): JsonResponse
    {
        $request = $this->handleFormRequest($request);
        $errors = $request->validate();

        if ($errors) {
            return $this->responseService->sendError("Falha na validação", $errors);
        }

        $addressData = PostalCodeClientService::make()->get($request->postal_code); // Fetch city and state

        $requestParameters = array_merge(
            $request->all(),
            (array) $addressData->getData()
        );

        $user = new User($requestParameters);
        $account = $user->type == Account::ARTIST ? new Artist : new Enterprise;
        $account->fill($requestParameters)->id = $user->id; // build artist or enterprise

        DB::transaction(function () use ($user, $account) {
            $user->save();
            $account->id = $user->id;
            $account->save();
        });

        $request->id = $user->id;
        $request->token = $user->token;

        return $this->responseService->sendMessage("User $user->id created", $this->show($request)->toArray());
    }

    public function show(Request $request): JsonResponse
    {
        $user = match (Account::tryFrom((string) $request->type)) {
            Account::ARTIST => new ArtistUserView,
            Account::ENTERPRISE => new EnterpriseUserView,
            default => new User
        };

        $user = $user->findOr($request->id, fn() => NotFoundRecordException::throw("User $request->id not found")); // Fetch by PK
        $user->makeVisibleIf($request->token !== null && $user->token === $request->token, ['phone', 'cnpj', 'cpf']);

        return $this->responseService->sendMessage("Search finished without errors", $user);
    }

    public function update(FormRequest $request): JsonResponse
    {
        $request = $this->handleFormRequest($request);

        if ($errors = $request->validate()) {
            return $this->responseService->sendError("Validate failed", $errors);
        }

        $user = User::where([
            'id' => $request->id,
            'token' => $request->token,
        ])->first();

        $addressData = $request->exists('postal_code') ? (array) PostalCodeClientService::make()->get($request->postal_code)->getData() : [];

        $userData = array_filter(
            array_merge(
                $request->all(),
                $addressData
            ),
            fn($item) => !is_null($item)
        );

        DB::transaction(function () use ($user, $userData) {
            $user->fill($userData)->save();
            ($user->type == Account::ARTIST ? new Artist : new Enterprise)
                ->where('id', $user->id)
                ->first()
                ->fill($userData)
                ->save();
        });

        $response = $this->responseService::from($this->show($request));
        $response->updateResponseMessage("User $user->id updated");
        return $response->resend();
    }

    public function destroy(FormRequest $request): JsonResponse
    {
        $user = User::where([
            'id' => $request->id,
            'token' => $request->token,
        ]);

        return $user->delete() ? $this->responseService->sendMessage("User $request->id deleted") : $this->responseService->sendError("User $request->id not deleted", ["the token is not valid"]);
    }
}
