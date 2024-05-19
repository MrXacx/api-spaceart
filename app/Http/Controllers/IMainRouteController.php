<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

abstract class IMainRouteController extends IRouteController
{
    /**
     * Display the all resources.
     *
     * @return Collection<Model>
     */
    abstract public function index(): JsonResponse;
}
