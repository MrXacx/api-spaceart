<?php
namespace App\Util\Exception;

use App\Util\Exception\DatabaseException;

class NoReturnRetrieveException extends DatabaseException{
    public static function throw (): void
    {
        throw new NoReturnRetrieveException('Consulta ao banco não obteve resposta');
    }
}