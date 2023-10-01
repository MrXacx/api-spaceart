<?php
namespace App\Util\Exception;

use App\Util\Exception\DatabaseException;

class NoReturnRetrieve extends DatabaseException{
    public static function throw (): void
    {
        throw new NoReturnRetrieve('Consulta ao banco não obteve resposta');
    }
}