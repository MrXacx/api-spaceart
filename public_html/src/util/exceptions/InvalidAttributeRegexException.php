<?php
namespace App\Util\Exception;

class InvalidAttributeRegexException extends InvalidAttributeFormatException
{
    public static function throw (string $attribute, string $file): void
    {
        parent::throw($attribute, parent::REGEX, $file);
    }
}

?>