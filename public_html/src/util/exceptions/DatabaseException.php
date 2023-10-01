<?php
namespace App\Util\Exception;

class DatabaseException extends \Exception
{
    public static function throw (string $message): void
    {
        throw new DatabaseException($message);     
    }
}

?>