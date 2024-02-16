<?php

namespace App\Exceptions;

use Exception;

abstract class HttpRequestException extends Exception {
  abstract public static function throw($message);
}
