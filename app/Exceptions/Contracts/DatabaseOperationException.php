<?php

namespace App\Exceptions\Contracts;

use App\Services\Logger;
use Exception;
use Psr\Log\LogLevel;

abstract class DatabaseOperationException extends Exception
{
    public static function throw(string $message): never
    {
        throw new static($message);
    }

    public function report(): void
    {
        $logger = new Logger;
        $logger->db($this->message, LogLevel::NOTICE);
    }
}
