<?php

namespace App\Repositories;

use App\Enumerate\AgreementStatus;
use App\Exceptions\CheckDBOperationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotSavedModelException;
use App\Models\Agreement;
use Closure;
use Illuminate\Database\Eloquent\Collection;

class AgreementRepository implements Contracts\IAgreementRepository
{
    public function list(string|int $userID, int $limit): Collection|array
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

        if (! $agreement->enterprise->active) {
            CheckDBOperationException::throw("The enterprise's account $agreement->enterprise_id is disabled");
        } elseif (! $agreement->artist->active) {
            CheckDBOperationException::throw("The artist's account $agreement->artist_id is disabled");
        }

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

        throw_unless($agreement->enterprise->active, new CheckDBOperationException("The enterprise's account $this->enterprise_id is disabled"));
        throw_unless($agreement->artist->active, new CheckDBOperationException("The artist's account $this->artist_id is disabled"));

        [$start] = $agreement->getActiveInterval();

        throw_unless($start->isFuture(), new CheckDBOperationException('The contracted service has already started.'));

        $status = $data['status'];
        unset($data['status']);

        // Change agreement status to 'send' if agreement conditions have changed
        $status = count($data) ? AgreementStatus::SEND->value : $status;

        throw_unless($agreement->update($data + ['status' => $status]), NotSavedModelException::class);

        return $agreement;
    }

    public function delete(int|string $id, Closure $validate): bool
    {
        $agreement = $this->fetch($id);
        $validate($agreement);

        return $agreement->delete();
    }
}
