<?php

namespace App\Exceptions;

use Exception;

abstract class HttpRequestException extends Exception
{
    public static function throw(string $message){
        throw new static($message);
    }
}
