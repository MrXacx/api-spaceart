<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Ramsey\Collection\Collection;

class ResponseService
{
    private function send(array $response, int $status = 200): JsonResponse
    {
        return response()->json($response, $status, [], JSON_PRETTY_PRINT);
    }

    public function sendMessage(string $message, array|Collection|Model $data = [], int $status = 200): JsonResponse
    {
        return $this->send([
            'message' => $message,
            'data' => $data instanceof Model ? [$data] : $data,
            'errors' => [],
            'fails' => false,
        ], $status);
    }

    public function sendError(string $message, array|Collection $errors = [], int $status = 422): JsonResponse
    {
        return $this->send([
            'message' => $message,
            'data' => [],
            'errors' => $errors,
            'fails' => true,
        ], $status);
    }
}
