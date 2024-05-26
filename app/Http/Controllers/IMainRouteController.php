<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

abstract class IMainRouteController extends IRouteController
{


    abstract public function index(Request $request): JsonResponse;

    abstract protected function fetch(string|int $id): Model;
}
