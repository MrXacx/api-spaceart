<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{

  private JsonResponse $response;


  public static function make(): self
  {
    return new ResponseService;
  }

  public static function from(JsonResponse $response): self {
    $service = static::make();
    $service->response = $response;
    return $service;
  }

  public function resend(): JsonResponse {
    return $this->response;
  }

  private function send(array $response, int $status = 200)
  {
    return $this->response = response()->json($response, $status, [], JSON_PRETTY_PRINT);
  }

  public function updateResponseMessage(string $message): void
  {
    $this->response->setData(array_merge(
      (array) $this->response->getData(),
      ["message" => $message],
    ));

  }

  public function sendMessage(string $message, array $data = []): JsonResponse
  {
    return $this->send([
      "message" => $message,
      "data" => $data,
      "fails" => false
    ]);
  }

  public function sendError(string $message, array $errors = [], int $status = 422): JsonResponse
  {
    return $this->send([
      "message" => $message,
      "errors" => $errors,
      "fails" => true
    ], $status);
  }
}