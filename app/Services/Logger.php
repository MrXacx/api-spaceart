<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Psr\Log\LogLevel;


class Logger
{
    private function log(string $level, string $channel, string $message, array $options = []): void
    {
        Log::channel($channel)->withoutContext()->$level($message, $options);
    }

    public function request(string $message, string $level = LogLevel::INFO): void
    {
        $request = request();
        $this->log(
            $level, 'request', '[{user}] - {message} - {method}:{uri}', [
                'user' => auth()->id() ?? 'N/A',
                'message' => $message,
                'method' => $request->getMethod(),
                'uri' => $request->getUri(),
            ]);
    }

    public function db(string $message, string $level = LogLevel::INFO): void
    {
        $this->log($level, 'db', $message);
    }

    public function error(string $message, string $level = LogLevel::WARNING): void
    {
        $this->log($level, 'error', $message);
    }

    public function auth(string $message, string $level = LogLevel::INFO): void
    {
        $this->log($level, 'auth', '[{user}] - {message}', [
            'user' => auth()->id() ?? 'N/A',
            'message' => $message,
        ]);
    }
}
