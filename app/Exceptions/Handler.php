<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\Contracts\DBQueryException;
use App\Exceptions\Contracts\HttpRequestException;
use App\Services\Logger;
use App\Services\ResponseService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Psr\Log\LogLevel;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function __construct(
        Container $container,
        private readonly ResponseService $responseService,
        private readonly Logger $logger
    ) {
        parent::__construct($container);
    }

    public function report(Throwable $e): void
    {
        if (method_exists($e, 'report')) {
            $e->report();
        } elseif ($e instanceof ValidationException) {
            $this->logger->request($e->getMessage());
        } elseif ($e instanceof AuthenticationException || $e instanceof AuthorizationException) {
            $this->logger->request($e->getMessage(), LogLevel::NOTICE);
        } else {
            $message = '['.$e::class.'] - '.$e->getMessage().' - '.$e->getFile().'::'.$e->getLine();
            if ($e instanceof Exception) {
                $this->logger->error($message, LogLevel::ALERT);
            } else {
                $this->logger->error($message, LogLevel::CRITICAL);
            }
        }
        parent::report($e);
    }

    public function render($request, Throwable $e): JsonResponse
    {
        if ($e instanceof DBQueryException || $e instanceof ValidationException) {
            return $this->responseService->sendError($e->getMessage());
        } elseif ($e instanceof HttpRequestException) {
            return $this->responseService->sendError($e->getMessage(), status: $e->getCode());
        } elseif ($e instanceof UnauthorizedHttpException) {
            return $this->responseService->sendError($e->getMessage(), status: 401);
        } elseif ($e instanceof AuthenticationException) {
            return $this->responseService->sendError($e->getMessage(), status: 403);
        }

        return $this->responseService
            ->sendError(
                'Unexpected error',
                [$e::class, $e->getMessage()],
                500
            );
    }
}
