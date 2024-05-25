<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Middleware\RequestMapper;
use App\Services\ResponseService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ControllerMiddlewareOptions;

/**
 * @OA\Server(url="https://localhost/api")
 * @OA\Info(title="SpaceArt API", version="2.0.0")
 */
abstract class IRouteController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(protected readonly ResponseService $responseService)
    {
        $this->middleware(RequestMapper::class);
        $this->setSanctumMiddleware();
    }

    protected function setSanctumMiddleware(): ControllerMiddlewareOptions
    {
        return $this->middleware('auth:sanctum');
    }
}
