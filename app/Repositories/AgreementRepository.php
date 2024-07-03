<?php

namespace App\Repositories;

use App\Enumerate\AgreementStatus;
use App\Exceptions\CheckDBOperationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotSavedModelException;
use App\Exceptions\TrashedModelReferenceException;
use App\Models\Agreement;
use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Collection;

class AgreementRepository implements Contracts\IAgreementRepository
{
    public function list(int|string $userID, int|string $limit): Collection|array
    {
        return Agreement::withAllRelations()
            ->where('artist_id', '=', $userID)
            ->orWhere('enterprise_id', '=', $userID)
            ->limit($limit)
            ->get();
    }

    public function fetch(int|string $id, Closure $validate): Agreement
    {
        $agreement = Agreement::findOr($id, fn () => NotFoundException::throw("Agreement $id was not found"))->loadAllRelations();
        $validate($agreement);

        throw_if($agreement->enterprise->trashed(), TrashedModelReferenceException::class, "The enterprise's account $agreement->enterprise_id is disabled");
        throw_if($agreement->artist->trashed(), TrashedModelReferenceException::class, "The artist's account $agreement->artist_id is disabled");

        return $agreement;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data, Closure $validate): Agreement
    {
        $agreement = new Agreement($data);
        $validate($agreement);
        $agreement->art_id = $agreement->artist->artistAccountData->art_id;
        throw_unless($agreement->save(), NotSavedModelException::class);

        return $agreement->load('artist', 'enterprise', 'art');
    }

    /**
     * {@inheritDoc}
     *
     * @throws CheckDBOperationException
     */
    public function update(int|string $id, array $data, Closure $validate): Agreement
    {
        $agreement = $this->fetch($id, $validate);

        $validate($agreement);

        throw_if($agreement->enterprise->trashed(), TrashedModelReferenceException::class, "The enterprise's account $this->enterprise_id is disabled");
        throw_if($agreement->artist->trashed(), TrashedModelReferenceException::class, "The artist's account $this->artist_id is disabled");

        [$start] = $agreement->getActiveInterval();

        throw_unless($start->isFuture(), CheckDBOperationException::class, 'The contracted service has already started.');

        $status = $data['status'];
        unset($data['status']);

        // Change agreement status to 'send' if agreement conditions have changed
        $status = count($data) ? AgreementStatus::SEND->value : $status;

        throw_unless($agreement->update($data + ['status' => $status]), NotSavedModelException::class);

        return $agreement;
    }

    public function delete(int|string $id, Closure $validate, ?Carbon $now = null): bool
    {
        $now ??= now();

        $agreement = $this->fetch($id);
        $validate($agreement);

        throw_if($agreement->isActive($now), NotSavedModelException::class, "The agreement $agreement->id is active for the user");
        throw_if($agreement->getActiveInterval()['end_moment']->isBefore($now));

        return $agreement->delete();
    }
}
