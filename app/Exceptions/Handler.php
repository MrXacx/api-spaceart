<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\Contracts\DatabaseOperationException;
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
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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
        switch ($e::class) {
            case ValidationException::class:
            case UnknownAccountTypeException::class:
                $this->logger->request($e->getMessage());
                break;
            case AuthenticationException::class:
            case AuthorizationException::class:
                $this->logger->request($e->getMessage(), LogLevel::NOTICE);
                break;
            default:
                if (method_exists($e, 'report')) {
                    $e->report();
                    break;
                }

                $message = '['.$e::class.'] - '.$e->getMessage().' - '.$e->getFile().'::'.$e->getLine();
                if ($e instanceof Exception) {
                    $this->logger->error($message, LogLevel::ALERT);
                    break;
                }
                $this->logger->error($message, LogLevel::CRITICAL);
        }

        parent::report($e);
    }

    public function render($request, Throwable $e): JsonResponse
    {
        if ($e instanceof DatabaseOperationException || $e instanceof ValidationException) {
            $code = 422;
        } elseif ($e instanceof AuthorizationException || $e instanceof AuthenticationException) {
            $code = 401;
        } elseif ($e instanceof HttpExceptionInterface) {
            $code = $e->getStatusCode();
        } else {
            return $this->responseService->sendError('Unexpected error', ['message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }

        return $this->responseService->sendError($e->getMessage(), [], $code);
    }
}
