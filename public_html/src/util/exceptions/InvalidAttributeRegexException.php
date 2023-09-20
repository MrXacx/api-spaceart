<?php
namespace App\Util\Exception;

class InvalidAttributeRegexException extends \App\Util\Exception\Template\InvalidAttributeFormatException
{
    public static function throw (string $attribute, string $file): void
    {
        new InvalidAttributeRegexException($attribute, parent::REGEX, $file);
    }
}

?>