<?php

namespace App\Exceptions;

class NotFoundException extends HttpRequestException
{
  protected $code = 404;
}
