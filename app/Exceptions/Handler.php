<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;
use Psr\Log\LogLevel;
use App\Services\Logger;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    public function report(Throwable $e): void
    {
        $logger = new Logger;

        if (method_exists($e, 'report')) {
            $e->report();
        } elseif ($e instanceof ValidationException) {
            $logger->request($e->getMessage());
        } elseif ( $e instanceof AuthenticationException || $e instanceof AuthorizationException ) {
            $logger->request($e->getMessage(), LogLevel::NOTICE);
        } else {
            $message = '[' . $e::class . '] - ' . $e->getMessage() . ' - ' . $e->getFile() . '::' . $e->getLine();
            if ($e instanceof Exception) {
                $logger->error($message, LogLevel::ALERT);
            } else {
                $logger->error($message, LogLevel::CRITICAL);
            }
        }
        parent::report($e);
    }

    public function render($request, Throwable $e): JsonResponse
    {
        $serviceResponse = ResponseService::make();
        if ($e instanceof DBQueryException || $e instanceof ValidationException) {
            return $serviceResponse->sendError($e->getMessage());
        } elseif ($e instanceof HttpRequestException) {
            return $serviceResponse->sendError($e->getMessage(), status: $e->getCode());
        } elseif ($e instanceof UnauthorizedHttpException) {
            return $serviceResponse->sendError($e->getMessage(), status: 401);
        } elseif ($e instanceof AuthenticationException) {
            return $serviceResponse->sendError($e->getMessage(), status: 403);
        }

        return $serviceResponse
            ->sendError(
                'Unexpected error',
                [$e::class, $e->getMessage()],
                500
            );
    }
}
