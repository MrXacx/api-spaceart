<?php

namespace App\Exceptions;

class UnprocessableEntityException extends HttpRequestException
{
    public static function throw($message)
    {
        throw new UnprocessableEntityException($message);
    }
}
