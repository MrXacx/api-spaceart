<?php

namespace App\Repositories\Contracts;

use App\Exceptions\CheckDBOperationException;
use App\Exceptions\NotSavedModelException;
use App\Models\SelectiveCandidate;
use Closure;

interface ISelectiveCandidateRepository
{
    /**
     * @throws NotSavedModelException
     * @throws CheckDBOperationException
     */
    public function create(array $data, Closure $validate): SelectiveCandidate;
}
