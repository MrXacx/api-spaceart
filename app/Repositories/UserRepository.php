<?php

namespace App\Repositories;

use App\Enumerate\Account;
use App\Exceptions\Contracts\HttpRequestException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotSavedModelException;
use App\Models\Agreement;
use App\Models\Art;
use App\Models\Artist;
use App\Models\Enterprise;
use App\Models\User;
use Closure;
use Exception;
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
            $isSuccessfulRelationshipDeletion = $this->deleteAgreementRelationships($user->artistAccountData ?? $user->enterpriseAccountData, $validate);
            throw_unless($isSuccessfulRelationshipDeletion && $user->delete(), NotSavedModelException::class);
            DB::commit();

            return true;
        } catch (NotSavedModelException) {
            DB::rollBack();

            return false;
        }
    }

    public function deleteAgreementRelationships(Artist|Enterprise $user, Closure $validate): bool
    {
        $now = now();
        $agreementRepository = new AgreementRepository;
        $validate = fn (Agreement $agreement) => $validate($agreement->enterprise);

        try {
            $user->agreements
                ->each(fn ($a) => throw_unless($agreementRepository->delete($a->id, $validate, $now), NotSavedModelException::class));

            return true;
        } catch (Exception) {
            return false;
        }
    }
}
