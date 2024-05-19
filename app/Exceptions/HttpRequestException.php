<?php

namespace App\Exceptions;

use App\Services\Logger;
use Exception;
use Psr\Log\LogLevel;

abstract class HttpRequestException extends Exception
{
    public static function throw(string $message)
    {
        throw new static($message);
    }

    public function report(): void
    {
        $logger = new Logger;
        $logger->request($this->getMessage(), LogLevel::NOTICE);
    }
}
