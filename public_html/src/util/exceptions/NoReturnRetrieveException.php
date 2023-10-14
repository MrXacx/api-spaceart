<?php
namespace App\Util\Exception;

class NoReturnRetrieveException extends DatabaseException{
    public static function throw (string $message = 'Consulta ao banco não obteve resposta'): void
    {
        throw new NoReturnRetrieveException($message);
    }
}

?>