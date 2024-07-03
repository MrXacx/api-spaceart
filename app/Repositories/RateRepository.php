<?php

namespace App\Repositories;

use App\Exceptions\CheckDBOperationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotSavedModelException;
use App\Models\Rate;
use Carbon\Carbon;
use Closure;

class RateRepository implements Contracts\IRateRepository
{
    /**
     * {@inheritDoc}
     */
    public function fetch(int|string $userID, int|string $agreementID): Rate
    {
        $rate = Rate::find([$userID, $agreementID]);
        throw_unless($rate, new NotFoundException("user $userID's rate was not found on agreement $agreementID"));

        return $rate->loadAllRelations();
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data, Closure $validate): Rate
    {
        $now = Carbon::now();
        $rate = new Rate($data);
        $validate($rate);

        $user = $rate->author->type;
        $rate->rated_id = $rate->agreement->$user->id;

        if (! $this->author->active) {
            CheckDBOperationException::throw("The author's account $this->author_id is disabled");
        }

        [,$endMoment] = $this->agreement->getActiveInterval();
        throw_unless(
            $now->isBefore($endMoment),
            new CheckDBOperationException("The agreement $this->agreement_id is not finished")
        );

        throw_unless($rate->save(), NotSavedModelException::class);

        return $rate->loadAllRelations();
    }

    /**
     * {@inheritDoc}
     */
    public function update(int|string $userID, int|string $agreementID, array $data, Closure $validate): Rate
    {
        $rate = $this->fetch($userID, $agreementID);
        $validate($rate);

        if (! $this->author->active) {
            CheckDBOperationException::throw("The author's account $this->author_id is disabled");
        }

        throw_unless($rate->update($data), NotSavedModelException::class);

        return $rate;
    }

    public function delete(int|string $userID, int|string $agreementID, Closure $validate): bool
    {
        $rate = $this->fetch($userID, $agreementID);
        $validate($rate);

        return $rate->delete();
    }
}
