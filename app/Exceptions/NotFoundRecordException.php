<?php

namespace App\Exceptions;

class NotFoundRecordException extends DBQueryException
{
    public static function throw($message) {
        throw new NotFoundRecordException($message);
    }
}
