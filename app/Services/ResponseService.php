<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class ResponseService
{

  public static function make(): self
  {
    return new ResponseService;
  }

  private function send(array $response, int $status = 200)
  {
    return response()->json($response, $status, [], JSON_PRETTY_PRINT);
  }

  public function sendMessage(string $message, array|Model $data = [], int $status = 200): JsonResponse
  {
    return $this->send([
      "message" => $message,
      "data" => is_array($data) ? $data : [$data],
      "errors" => [],
      "fails" => false
    ], $status);
  }

  public function sendError(string $message, array $errors = [], int $status = 422): JsonResponse
  {
    return $this->send([
      "message" => $message,
      "data" => [],
      "errors" => $errors,
      "fails" => true
    ], $status);
  }
}