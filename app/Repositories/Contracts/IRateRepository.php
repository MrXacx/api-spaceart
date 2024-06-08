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
    public function fetch(string|int $userID, string|int $agreementID): Rate;

    /**
     * @throws NotSavedModelException
     * @throws CheckDBOperationException
     */
    public function create(array $data, Closure $validate): Rate;

    /**
     * @throws NotSavedModelException
     * @throws CheckDBOperationException
     */
    public function update(string|int $userID, string|int $agreementID, array $data, Closure $validate): Rate;

    public function delete(string|int $userID, string|int $agreementID, Closure $validate): bool;
}
