<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class IMainRouteController extends IRouteController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    abstract public function index(Request $request): JsonResponse;
}
