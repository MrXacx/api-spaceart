<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class IMainRouteController extends IRouteController
{
    /**
     * Display the all resources.
     */
    abstract public function index(Request $request): JsonResponse;

    abstract protected function fetch(string|int $id): Model;
}
