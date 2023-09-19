<?php
namespace App\Util\Exception;

class InvalidAttributeLengthException extends InvalidAttributeFormatException
{
    public static function throw (string $attribute, string $file): void
    {
        parent::throw($attribute, parent::LENGTH, $file);
    }
}

?>