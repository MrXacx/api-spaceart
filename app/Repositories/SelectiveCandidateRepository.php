<?php

namespace App\Repositories;

use App\Exceptions\CheckDBOperationException;
use App\Exceptions\NotSavedModelException;
use App\Exceptions\TrashedModelReferenceException;
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

        throw_if($candidature->artist->trashed(), TrashedModelReferenceException::class, "The artist's account $candidature->artist_id is disabled");
        throw_if($candidature->enterprise->trashed(), TrashedModelReferenceException::class, "The enterprise's account {$candidature->selective->enterprise_id} is disabled");

        throw_if(
            $this->artist->artistAccountData->art !== $this->selective->art, CheckDBOperationException::class,
            "The {$this->artist->artistAccountData->art->name} account is not able to {$this->selective->art->name} selective"
        );

        throw_unless($candidature->selective->isActive($now), CheckDBOperationException::class, "selective $candidature->selective_id is closed");

        throw_unless($candidature->save(), NotSavedModelException::class);

        return $candidature;
    }
}
