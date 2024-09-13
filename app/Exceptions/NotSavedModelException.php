<?php

namespace App\Exceptions;

use App\Exceptions\Contracts\DatabaseOperationException;

class NotSavedModelException extends DatabaseOperationException
{
}
