<?php

namespace App\Repositories;

use App\Enumerate\Account;
use App\Exceptions\Contracts\HttpRequestException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotSavedModelException;
use App\Exceptions\UnprocessableEntityException;
use App\Models\Agreement;
use App\Models\Art;
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

        throw_if(
            $user->deleted_at, // Unless account is active
            new UnprocessableEntityException("User $id's account is disabled")
        );

        if (auth()->user()?->id == $id) {
            $user->showConfidentialData();
        }

        return $user->loadAllRelations();
    }

    public function list(int|string $offset, int|string $limit, string $startWith = ''): Collection|array
    {
        return User::withAllRelations()
            ->where('id', '>', $offset)
            ->where('name', 'REGEXP', "^$startWith")
            ->limit($limit)
            ->get();
    }

    public function create(array $data): User
    {
        if ($data['art']) {
            $data['art_id'] = Art::where('name', $data['art'])->first()->id;
        }

        $address = PostalCodeRepository::make()->fetch($data['postal_code']); // Fetch city and state
        $data += $address->toArray(); // Merge request data and zip code API response

        try {
            DB::beginTransaction();
            $user = new User($data); // Build user
            throw_unless($user->save($data), NotSavedModelException::class);
            $data += $user->toArray();

            match ($user->type) {
                Account::ARTIST => $this->createArtist($data),
                Account::ENTERPRISE => $this->createEnterprise($data),
            };

            DB::commit();

            return $user;
        } catch (NotSavedModelException $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createArtist(array $data): Artist
    {
        $artist = new Artist($data);
        throw_unless($artist->save(), NotSavedModelException::class);

        return $artist;
    }

    public function createEnterprise(array $data): Enterprise
    {
        $enterprise = new Enterprise($data);
        throw_unless($enterprise->save(), NotSavedModelException::class);

        return $enterprise;
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

        DB::beginTransaction();
        try {
            $isSuccessfulRelationshipDeletion = $this->deleteAgreementRelationships($user->artistAccountData ?? $user->enterpriseAccountData);
            throw_unless($isSuccessfulRelationshipDeletion && $user->delete(), NotSavedModelException::class);
            DB::commit();

            return true;
        } catch (NotSavedModelException) {
            DB::rollBack();
            return false;
        }
    }

    public function deleteAgreementRelationships(Artist|Enterprise $user): bool
    {
        $now = now();
        $agreements = $user->agreements;

        throw_if( // Not delete if exists an agreement active
            $agreements->contains(fn ($a) => $a->isActive($now)),
            new NotSavedModelException('An agreement is active for this user')
        );

        return $agreements
            ->filter(fn (Agreement $a) => $now->isBefore($a->getActiveInterval()['start_moment']))
            ->reduce(fn (bool $isSuccessful, Agreement $a) => $isSuccessful && $a->delete(), true);
    }
}
