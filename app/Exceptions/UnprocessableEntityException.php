<?php

namespace App\Exceptions;

use App\Exceptions\Contracts\HttpRequestException;

class UnprocessableEntityException extends HttpRequestException
{
    protected $code = 422;
}
