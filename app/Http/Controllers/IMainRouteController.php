<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ResponseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\ControllerMiddlewareOptions;

abstract class IMainRouteController extends IRouteController
{
    /**
     * Display the all resources.
     *
     * @return Collection<Model>
     */
    abstract public function index(): JsonResponse;
}
