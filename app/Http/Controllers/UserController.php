<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Artist;
use Enumerate\Account;
use App\Models\Enterprise;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\User\ArtistRequest;
use App\Models\ViewModels\ArtistUserView;
use App\Exceptions\NotFoundRecordException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\User\EnterpriseRequest;
use App\Models\ViewModels\EnterpriseUserView;
use App\Exceptions\UnprocessableEntityException;
use App\Services\Clients\PostalCodeClientService;

class UserController extends IController
{
    private function handleFormRequest(FormRequest $request): ArtistRequest|EnterpriseRequest
    {
        return match (Account::tryFrom((string) $request->type)) { // Find correct request for account type
            Account::ARTIST => ArtistRequest::createFrom($request),
            Account::ENTERPRISE => EnterpriseRequest::createFrom($request),
            default => UnprocessableEntityException::throw("Account type not found."),
        };
    }

    /**
     * Display a listing of the resource.
     */
    public function list(): Collection|JsonResponse
    {
        return User::all();
    }

    public function store(FormRequest $request): User|JsonResponse
    {

        $request = $this->handleFormRequest($request);
        $errors = $request->validate();

        try {
            if ($errors) {
                return response()->json($errors);
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
            return $this->show($request);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), options: JSON_INVALID_UTF8_IGNORE);
        }
    }

    public function show(Request $request): User|JsonResponse
    {
        $user = match (Account::tryFrom((string) $request->type)) {
            Account::ARTIST => new ArtistUserView,
            Account::ENTERPRISE => new EnterpriseUserView,
            default => new User
        };

        $user = $user->findOr($request->id, fn() => NotFoundRecordException::throw("user $request->id were not found")); // Fetch by PK
        $user->makeVisibleIf($request->token !== null && $user->token === $request->token, ['phone', 'cnpj', 'cpf']);

        /* if () {
             $user->makeVisible(); // Turn visible
         }*/

        return $user;
    }

    public function update(FormRequest $request): User|JsonResponse
    {
        $request = $this->handleFormRequest($request);

        if ($errors = $request->validate()) {
            return response()->json($errors);
        }

        $user = User::where('token', $request->token)->first();

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

        return $this->show($request);
    }


    public function destroy(FormRequest $request): JsonResponse
    {

        $user = User::where([
            'id' => $request->id,
            'token' => $request->token,
        ]);

        if ($user->delete()) {

            $message = "The user $request->id were not deleted";

        } else {
            $message = "The user $request->id were deleted";
        }

        return response()->json(['message' => $message]);

    }
}
