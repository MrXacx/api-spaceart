<?php

namespace App\Repositories;

use App\Enumerate\Account;
use App\Exceptions\Contracts\HttpRequestException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotSavedModelException;
use App\Exceptions\UnprocessableEntityException;
use App\Models\Artist;
use App\Models\Enterprise;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository implements Contracts\IUserRepository
{
    /**
     * @throws HttpRequestException
     */
    public function fetch(int|string $id): User
    {
        $user = User::findOr($id, fn () => NotFoundException::throw("User $id was not found")); // Fetch by PK

        throw_unless(
            $user->active, // Unless account is active
            new UnprocessableEntityException("User $id's account is disabled")
        );

        if (auth()->user()?->id == $id) {
            $user->showConfidentialData();
        }

        return $user->loadAllRelations();
    }

    public function list(int $offset, int $limit, string $startWith = ''): Collection|array
    {
        return User::withAllRelations()
            ->where('id', '>', $offset)
            ->where('active', true)
            ->where('name', 'REGEXP', "^{$startWith}")
            ->limit($limit)
            ->get();
    }

    public function create(array $data): User
    {
        $address = PostalCodeRepository::make()->fetch($data['postal_code']); // Fetch city and state
        $data += $address->toArray(); // Merge request data and zip code API response

        DB::beginTransaction();
        $user = new User($data); // Build user
        throw_unless($user->save($data), NotSavedModelException::class);
        $typedUser = $data['type'] == Account::ARTIST ? new Artist : new Enterprise;
        $typedUser->fill($data + ['id' => $user->id]);
        throw_unless($typedUser->save($data), NotSavedModelException::class);
        DB::commit();

        return $user;
    }

    public function update(int|string $id, array $data, Closure $validate): User
    {
        if (array_keys($data, 'postal_code')) { // Fetch information derived from the zip code
            $data += PostalCodeRepository::make()->fetch($data['postal_code'])->toArray();
        }

        $user = $this->fetch($id); // Fetch user
        $validate($user);

        DB::transaction(function () use ($user, $data) {
            $typedUser = $user->artistAccountData ?? $user->enterpriseAccountData;
            throw_unless($user->update($data) && $typedUser->update($data), NotSavedModelException::class);
        });

        return $user;
    }

    public function delete(int|string $id, Closure $validate): bool
    {
        $user = $this->fetch($id);
        $validate($user);

        return $user->update([
            'image' => null,
            'slug' => null,
            'active' => false,
        ]);
    }
}
