<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ViewModels\ArtistUserView;
use App\Models\ViewModels\EnterpriseUserView;
use Enumerate\Account;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        return User::all()->toJson();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $user = match (Account::tryFrom((string) $request->type)) {
            Account::ARTIST => new ArtistUserView,
            Account::ENTERPRISE => new EnterpriseUserView,
            default => new User
        };

        return
            (
                $user
                    ->all()
                    ->firstWhere(
                        fn ($user) => $user->id == $request->id
                    )
                ?? Collection::empty()
            )->toJson();
    }

    /**
     * Display the specified resource.
     */
    public function showPrivate(User $user)
    {
        return dd($user->toArray());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        return dd($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
