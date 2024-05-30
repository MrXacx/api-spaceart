<?php

namespace App\Repositories;

use App\Exceptions\CheckDBOperationException;
use App\Exceptions\NotSavedModelException;
use App\Models\SelectiveCandidate;
use Carbon\Carbon;
use Closure;

class SelectiveCandidateRepository implements Contracts\ISelectiveCandidateRepository
{
    /**
     * {@inheritDoc}
     */
    public function create(array $data, Closure $validate): SelectiveCandidate
    {
        $now = Carbon::now();

        $candidature = new SelectiveCandidate($data);
        $validate($candidature);

        if (! $candidature->artist->active) {
            CheckDBOperationException::throw("The artist's account $candidature->artist_id is disabled");
        } elseif (! $candidature->enterprise->active) {
            CheckDBOperationException::throw("The enterprise's account {$candidature->selective->enterprise_id} is disabled");
        }

        if ($this->artist->artistAccountData->art !== $this->selective->art) {
            CheckDBOperationException::throw("The {$this->artist->artistAccountData->art->name} account is not able to {$this->selective->art->name} selective");
        }

        [$startMoment, $endMoment] = $candidature->selective->getActiveInterval();

        if (! $now->isBetween($startMoment, $endMoment)) {
            CheckDBOperationException::throw("selective $candidature->selective_id is closed");
        }

        throw_unless($candidature->save(), NotSavedModelException::class);

        return $candidature;
    }
}
