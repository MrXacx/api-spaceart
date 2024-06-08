<?php

namespace App\Exceptions;

use App\Exceptions\Contracts\HttpRequestException;

class NotFoundException extends HttpRequestException
{
    protected $code = 404;
}
