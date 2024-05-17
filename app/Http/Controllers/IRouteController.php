<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ResponseService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class IRouteController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(protected ResponseService $responseService)
    {
        $this->setSanctumMiddleware();
    }

    protected function setSanctumMiddleware(): void
    {
        $this->middleware('auth:sanctum');
    }
}
