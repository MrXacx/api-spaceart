<?php

declare(strict_types=1);

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Parameter(
 *        parameter="Limit",
 *        name="limit",
 *        in="query",
 *        description="Limit per page",
 *        style="form",
 *
 *        @OA\Schema(type="integer", nullable=true),
 *       ),
 *
 * @OA\Parameter(
 *     parameter="Offset",
 *       name="offset",
 *       in="query",
 *       description="Offset for search",
 *
 *       @OA\Schema(type="integer", nullable=true),
 *       style="form"
 *      ),
 */
abstract class IMainRouteController extends IRouteController
{
    abstract public function index(Request $request): JsonResponse;
}
