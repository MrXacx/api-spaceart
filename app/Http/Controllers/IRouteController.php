<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ResponseService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ControllerMiddlewareOptions;

abstract class IRouteController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(protected ResponseService $responseService)
    {
        $this->setSanctumMiddleware();
    }

    protected function setSanctumMiddleware(): ControllerMiddlewareOptions
    {
        return $this->middleware('auth:sanctum');
    }
}