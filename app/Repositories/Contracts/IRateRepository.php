<?php

namespace App\Repositories\Contracts;

use App\Exceptions\DatabaseValidationException;
use App\Exceptions\NotFoundModelException;
use App\Exceptions\NotSavedModelException;
use App\Models\Rate;
use Closure;

interface IRateRepository
{
    /**
     * @throws NotFoundModelException
     * @throws DatabaseValidationException
     */
    public function fetch(int|string $userID, int|string $agreementID): Rate;

    /**
     * @throws NotSavedModelException
     * @throws DatabaseValidationException
     */
    public function create(array $data, Closure $validate): Rate;

    /**
     * @throws NotSavedModelException
     * @throws DatabaseValidationException
     */
    public function update(int|string $userID, int|string $agreementID, array $data, Closure $validate): Rate;

    public function delete(int|string $userID, int|string $agreementID, Closure $validate): bool;
}
