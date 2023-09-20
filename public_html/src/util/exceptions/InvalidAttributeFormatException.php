<?php
namespace App\Util\Exception;

use Exception;

class InvalidAttributeFormatException extends Exception
{
    protected const LENGTH = 0;
    protected const REGEX = 1;

    protected static function throw (string $attribute, int $code, string $file): void
    {
        $reason = match ($code) {
            self::LENGTH => 'Comprimento não condiz com o esperado',
            self::REGEX => 'Expressão regular não condiz com o esperado',
            default => 'Motivo indeterminado'
        };

        $attribute = strtolower($attribute);

        throw new InvalidAttributeFormatException(
            "Exceção lançada ao informar valor inválido para o atributo $attribute de $file. $reason.",
            $code
        );
    }

}

class InvalidAttributeLengthException extends InvalidAttributeFormatException
{
    public static function throw (string $attribute, string $file): void
    {
        parent::throw($attribute, parent::LENGTH, $file);
    }
}

class InvalidAttributeRegexException extends InvalidAttributeFormatException
{
    public static function throw (string $attribute, string $file): void
    {
        parent::throw($attribute, parent::REGEX, $file);
    }
}

?>