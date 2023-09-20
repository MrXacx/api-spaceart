<?php
namespace App\Util\Exception;

class InvalidAttributeLengthException extends \App\Util\Exception\Template\InvalidAttributeFormatException
{
    public static function throw (string $attribute, string $file): void
    {
        new InvalidAttributeLengthException($attribute, parent::LENGTH, $file);
    }
}

?>