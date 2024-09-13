<?php

namespace App\Http\Middleware;

use App\Services\Logger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestMapper
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $logger = new Logger;
        $logger->request("Accepted request from {$request->getClientIp()}");

        return $next($request);
    }
}
