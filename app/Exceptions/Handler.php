<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Services\ResponseService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
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
        if (
            $e instanceof DBQueryException ||
            $e instanceof HttpRequestException ||
            $e instanceof AuthorizationException ||
            $e instanceof ValidationException
        ) {
            return $serviceResponse->sendError($e->getMessage());
        }

        return $serviceResponse
            ->sendError(
                'Internal error! Please, report it on https://github.com/MrXacx/api-spaceart/issues/new/',
                [$e::class, $e->getMessage(), $e->getFile(), $e->getLine()],
                500
            );
    }
}
