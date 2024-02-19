<?php

namespace App\Exceptions;

use Exception;

abstract class DBQueryException extends Exception {
  abstract public static function throw($message);
}
