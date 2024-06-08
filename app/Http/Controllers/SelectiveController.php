<?php

namespace App\Http\Controllers;

use App\Exceptions\CheckDBOperationException;
use App\Exceptions\NotSavedModelException;
use App\Http\Controllers\Contracts\IMainRouteController;
use App\Http\Requests\SelectiveRequest;
use App\Repositories\SelectiveRepository;
use App\Services\ResponseService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ControllerMiddlewareOptions;
use OpenApi\Annotations as OA;

/**
 * @OA\Response(
 *     response="ReturnSelective",
 *     description="Operation was realized",
 *
 *     @OA\JsonContent(
 *
 *         @OA\Property(property="message", type="string"),
 *         @OA\Property(property="data", type="array", maxItems=1, @OA\Items(ref="#/components/schemas/Selective")),
 *         @OA\Property(property="fails", type="boolean", default="false"),
 *     )
 * )
 */
class SelectiveController extends IMainRouteController
{
    public function __construct(
        private readonly SelectiveRepository $selectiveRepository,
        ResponseService $responseService
    ) {
        parent::__construct($responseService);
    }

    protected function setSanctumMiddleware(): ControllerMiddlewareOptions
    {
        return parent::setSanctumMiddleware()->except('index');
    }

    /**
     * @OA\Get(
     *     tags={"Selective"},
     *     path="/selective",
     *     summary="List selectives",
     *     description="Fetch selectives on database",
     *
     *     @OA\Parameter(ref="#/components/parameters/Offset"),
     *     @OA\Parameter(ref="#/components/parameters/Limit"),
     *
     *     @OA\Response(
     *       response="200",
     *       description="Operation was realized",
     *
     *       @OA\JsonContent(
     *
     *           @OA\Property(property="message", type="string"),
     *           @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Selective")),
     *           @OA\Property(property="fails", type="boolean", default="false"),
     *       )
     *     ),
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => ['numeric', 'min:1', 'max:100', 'nullable'],
            'offset' => ['numeric', 'min:1', 'nullable'],
        ]);

        return $this->responseService->sendMessage(
            'Selectives found',
            $this->selectiveRepository->list((int) $request->offset, $request->limit ?? 20)
        );
    }

    /**
     * @OA\Post(
     *      tags={"Selective"},
     *      path="/selective",
     *      summary="Store selective",
     *      description="Store selective on database",
     *      security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *      @OA\RequestBody(ref="#/components/requestBodies/SelectiveStore"),
     *
     *      @OA\Response(response="200", ref="#/components/responses/ReturnSelective"),
     *  )
     *
     * @throws CheckDBOperationException
     * @throws AuthorizationException
     */
    public function store(SelectiveRequest $request): JsonResponse
    {
        try {
            $selective = $this->selectiveRepository->create(
                $request->validated(),
                fn ($s) => $this->authorize('isOwner', $s)
            );

            return $this->responseService->sendMessage('Selective was created', $selective->toArray(), 201);
        } catch (NotSavedModelException) {
            return $this->responseService->sendError('Selective was not created');
        }
    }

    /**
     * @OA\Get(
     *     tags={"Selective"},
     *     path="/selective/{id}",
     *     summary="Fetch unique selective",
     *     description="Fetch selective on database",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\Parameter(ref="#/components/parameters/Id"),
     *
     *     @OA\Response(response="200", ref="#/components/responses/ReturnSelective"),
     * )
     */
    public function show(SelectiveRequest $request): JsonResponse
    {
        return $this->responseService->sendMessage(
            "Selective $request->id found",
            $this->selectiveRepository->fetch($request->id)
        );
    }

    /**
     * @OA\Post(
     *    tags={"Selective"},
     *    path="/selective/{id}/update",
     *    summary="[PUT] /selective/{id} alias ",
     *    description="Redirect to [PUT] /selective/{id}",
     *    security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *    @OA\Parameter(ref="#/components/parameters/Id"),
     *
     *    @OA\Response(response="302", description="Redirected to [PUT] /selective/{id}"),
     * )
     *
     * @OA\Put(
     *     tags={"Selective"},
     *     path="/selective/{id}",
     *     summary="Update selective",
     *     description="Update selective on database",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\Parameter(ref="#/components/parameters/Id"),
     *
     *     @OA\RequestBody(ref="#/components/requestBodies/SelectiveUpdate"),
     *
     *     @OA\Response(response="200", ref="#/components/responses/ReturnSelective"),
     * )
     */
    public function update(SelectiveRequest $request): JsonResponse
    {
        try {
            $selective = $this->selectiveRepository->update(
                $request->validated(),
                fn ($s) => $this->authorize('isOwner', $s)
            );

            return $this->responseService->sendMessage('Selective updated', $selective);
        } catch (NotSavedModelException) {
            return $this->responseService->sendError('Selective not updated');
        }
    }

    /**
     * @OA\Post(
     *    tags={"Selective"},
     *    path="/selective/{id}/delete",
     *    summary="[DELETE] /selective/{id} alias ",
     *    description="Redirect to [DELETE] /selective/{id}",
     *    security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *    @OA\Parameter(ref="#/components/parameters/Id"),
     *
     *    @OA\Response(response="302", description="Redirected to [DELETE] /selective/{id}"),
     * )
     *
     * @OA\Delete(
     *     tags={"Selective"},
     *     path="/selective/{id}",
     *     summary="Delete selective",
     *     description="Delete selective on database",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\Parameter(ref="#/components/parameters/Id"),
     *
     *     @OA\Response(response="204", ref="#/components/responses/204"),
     * )
     */
    public function destroy(SelectiveRequest $request): JsonResponse
    {
        return $this->selectiveRepository->delete(
            $request->id,
            fn ($s) => $this->authorize('isAdmin', $s->enterprise)
        ) ?
            $this->responseService->sendMessage('Selective deleted', 204) :
            $this->responseService->sendError('Selective not deleted');
    }
}
