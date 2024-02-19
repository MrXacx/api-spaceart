<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Services\ResponseService;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];


    public function render($request, Throwable $e)
    {
        $serviceResponse = ResponseService::make();
        return $e instanceof DBQueryException || $e instanceof HttpRequestException ?
        $serviceResponse->sendError($e->getMessage()) :
        $serviceResponse->sendError("Internal error! Please, report it on https://github.com/MrXacx/api-spaceart/issues/new/", status: 500);
    }
    
}
