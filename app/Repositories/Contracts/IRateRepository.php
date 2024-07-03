<?php

namespace App\Repositories\Contracts;

use App\Exceptions\CheckDBOperationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotSavedModelException;
use App\Models\Rate;
use Closure;

interface IRateRepository
{
    /**
     * @throws NotFoundException
     * @throws CheckDBOperationException
     */
    public function fetch(int|string $userID, int|string $agreementID): Rate;

    /**
     * @throws NotSavedModelException
     * @throws CheckDBOperationException
     */
    public function create(array $data, Closure $validate): Rate;

    /**
     * @throws NotSavedModelException
     * @throws CheckDBOperationException
     */
    public function update(int|string $userID, int|string $agreementID, array $data, Closure $validate): Rate;

    public function delete(int|string $userID, int|string $agreementID, Closure $validate): bool;
}
