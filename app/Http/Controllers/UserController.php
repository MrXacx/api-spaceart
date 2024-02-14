<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Enumerate\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ViewModels\ArtistUserView;
use Illuminate\Database\Eloquent\Collection;
use App\Models\ViewModels\EnterpriseUserView;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(): Collection
    {
        return User::all();
    }


    public function store(Request $request): User
    {
        return $this->show($request);
    }


    public function show(Request $request)
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

    public function update(Request $request): User
    {
        return $this->show($request);
    }


    public function destroy(Request $user): void
    {
        dd($user);
    }
}
