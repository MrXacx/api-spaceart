<?php

namespace App\Repositories\Contracts;

use App\Exceptions\DatabaseValidationException;
use App\Exceptions\NotSavedModelException;
use App\Models\SelectiveCandidate;
use Closure;

interface ISelectiveCandidateRepository
{
    /**
     * @throws NotSavedModelException
     * @throws DatabaseValidationException
     */
    public function create(array $data, Closure $validate): SelectiveCandidate;
}
