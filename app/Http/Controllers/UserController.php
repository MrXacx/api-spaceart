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
use App\Exceptions\UnprocessableEntityException;
use App\Http\Requests\User\ArtistRequest;
use App\Models\ViewModels\ArtistUserView;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\User\EnterpriseRequest;
use App\Models\ViewModels\EnterpriseUserView;
use App\Services\Clients\PostalCodeClientService;

class UserController extends IController
{
    /**
     * Display a listing of the resource.
     */
    public function list(): Collection|JsonResponse
    {
        return User::all();
    }

    public function store(FormRequest $request): User|JsonResponse
    {
        $request = match (Account::tryFrom((string) $request->type)) { // Find correct request for account type
            Account::ARTIST => ArtistRequest::createFrom($request),
            Account::ENTERPRISE => EnterpriseRequest::createFrom($request),
            default => UnprocessableEntityException::throw("Account type not found."),
        };

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
            
            if ($user->save()) { // Resume if insert works
                
                $specificUser = $user->type == Account::ARTIST ? new Artist : new Enterprise;
                $specificUser->fill($requestParameters)->id = $user->id; // build artist or enterprise

                if ($specificUser->save()) { // Resume if insert works
                    DB::commit();
                    $request->id = $user->id;
                    $request->token = $user->token;
                    return $this->show($request);
                }
                
            }
            
            DB::rollBack();
            throw new \Exception("Usuário não foi criado");

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

        $user = $user->find($request->id); // Fetch by PK

        if ($user->token === $request->get('token')) {
            $user->makeVisible(['phone', 'cnpj', 'cpf']); // Turn visible
        }

        return $user;
    }

    public function update(FormRequest $request): User|JsonResponse
    {
        return $this->show($request);
    }


    public function destroy(FormRequest $user): JsonResponse
    {
        return response()->json($user);
    }
}
