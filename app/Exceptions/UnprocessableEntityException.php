<?php

namespace App\Exceptions;

class UnprocessableEntityException extends HttpRequestException
{
  protected $code = 422;
}
