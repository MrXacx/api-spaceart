<?php

namespace App\Exceptions;

use Exception;

abstract class DBQueryException extends Exception
{
    public static function throw(string $message) {
        throw new static($message);
    }
}

