<?php

namespace App\Repositories;

use App\Exceptions\DatabaseValidationException;
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

        throw_if(
            $candidature->artist->artistAccountData->art !== $candidature->selective->art, DatabaseValidationException::class,
            "The {$candidature->artist->artistAccountData->art->value} account is not able to {$candidature->selective->art->value} selective"
        );

        throw_unless($candidature->selective->isActive($now), DatabaseValidationException::class, "selective $candidature->selective_id is closed");

        throw_unless($candidature->save(), NotSavedModelException::class);

        return $candidature;
    }
}
