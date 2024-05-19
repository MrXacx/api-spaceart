<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Services\Logger;
use App\Services\ResponseService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Psr\Log\LogLevel;
use Throwable;

class Handler extends ExceptionHandler
{
    public function report(Throwable $e): void
    {
        $logger = new Logger;
        if ($e instanceof ValidationException) {
            $logger->request($e->getMessage());
        } elseif (
            $e instanceof AuthenticationException or
            $e instanceof AuthorizationException
        ) {
            $logger->request($e->getMessage(), LogLevel::NOTICE);
        } elseif ($e instanceof Exception) {
            $logger->error($e->getMessage(), LogLevel::ALERT);
        } else {
            $logger->error($e->getMessage(), LogLevel::CRITICAL);
        }
        parent::report($e);
    }

    public function render($request, Throwable $e): JsonResponse
    {
        $serviceResponse = ResponseService::make();
        if (
            $e instanceof DBQueryException ||
            $e instanceof HttpRequestException ||
            $e instanceof AuthorizationException ||
            $e instanceof ValidationException ||
            $e instanceof AuthenticationException

        ) {
            return $serviceResponse->sendError($e->getMessage());
        }

        return $serviceResponse
            ->sendError(
                'Internal error! Please, report it on https://github.com/MrXacx/api-spaceart/issues/new/',
                [$e->getMessage()],
                500
            );
    }
}
