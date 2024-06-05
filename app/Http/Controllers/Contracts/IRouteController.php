<?php

declare(strict_types=1);

namespace App\Http\Controllers\Contracts;

use App\Http\Middleware\RequestMapper;
use App\Services\ResponseService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ControllerMiddlewareOptions;
use OpenApi\Annotations as OA;

/**
 * @OA\Server(url="http://api-spaceart.local/api/")
 *
 * @OA\Info(title="SpaceArt API", version="2.0.0")
 *
 * @OA\Parameter(
 *      parameter="Id",
 *       name="id",
 *       in="path",
 *       description="Resource id",
 *       style="form",
 *       @OA\Schema(type="integer"),
 *   )
 *
 * @OA\Parameter(
 *      parameter="Author",
 *      name="author",
 *      in="path",
 *      description="Author id",
 *      style="form",
 *      @OA\Schema(type="integer"),
 *   )
 *
 * @OA\Response(
 *  response="204",
 *  description="Resource was disabled",
 *  @OA\JsonContent(
 *      type="object",
 *      @OA\Property(property="message", type="string"),
 *      @OA\Property(property="fails", type="bool", default="false"),
 *  )
 * )
 * @OA\Response(
 *     response="401",
 *     description="Authentication failed",
 *
 *     @OA\JsonContent(
 *      @OA\Property(property="message", type="string"),
 *      @OA\Property(property="fails", type="bool"),
 *     )
 * )
 * @OA\Response(
 *     response="422",
 *     description="Unprocessable entity",
 *
 *     @OA\JsonContent(
 *      @OA\Property(property="message", type="string"),
 *      @OA\Property(property="data", type="array", @OA\Items(minItems=1)),
 *      @OA\Property(property="fails", type="bool"),
 *     )
 * )
 * @OA\Response(
 *     response="500",
 *     description="Unexpected error",
 *
 *     @OA\JsonContent(@OA\Property(property="fails", type="bool"))
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="Sanctum",
 *     type="apiKey",
 *     scheme={"bearerToken": {}},
 *  )
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
