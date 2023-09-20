<?php
namespace App\Util\Exception\Template;

use Exception;

abstract class InvalidAttributeFormatException extends Exception
{
    protected const LENGTH = 0;
    protected const REGEX = 1;

    protected function __construct (string $attribute, int $code, string $file)
    {
        $reason = match ($code) {
            self::LENGTH => 'Comprimento não condiz com o esperado',
            self::REGEX => 'Expressão regular não condiz com o esperado',
            default => 'Motivo indeterminado'
        };

        $attribute = strtolower($attribute);

        parent::__construct("Exceção lançada ao informar valor inválido para o atributo $attribute de $file. $reason.");

        throw $this;
    }

}


?>