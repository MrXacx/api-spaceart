<?php

namespace App\Http\Controllers;


use App\Exceptions\NotSavedModelException;
use App\Http\Controllers\Contracts\IRouteController;
use App\Http\Requests\RateRequest;
use App\Repositories\RateRepository;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Parameter(
 *    parameter="Agreement",
 *    name="agreement",
 *    in="path",
 *    description="Agreement id",
 *    style="form",
 *    @OA\Schema(type="integer"),
 * )
 *
 * @OA\Response(
 *   response="ReturnRate",
 *   description="Response is successful",
 *
 *   @OA\JsonContent(
 *        @OA\Property(property="message", type="string"),
 *        @OA\Property(property="data", type="array", maxItems=1, @OA\Items(ref="#/components/schemas/Rate")),
 *        @OA\Property(property="fails", type="bool"),
 *    )
 *  )
 */
class RateController extends IRouteController
{
    public function __construct(
        ResponseService $responseService,
        private readonly RateRepository $rateRepository
    )
    {
        parent::__construct($responseService);
    }

    /**
     * @OA\Post(
     *     tags={"Rate"},
     *     path="/agreement/{agreement}/rate",
     *     summary="Store rate",
     *     description="Relates rate to agreement on database",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *     @OA\Parameter(ref="#/components/parameters/Agreement"),
     *     @OA\RequestBody(ref="#/components/requestBodies/RateStore"),
     *     @OA\Response(response="201", ref="#/components/responses/ReturnRate"),
     * )
     *
     * @throws CheckDBOperationException
     * @throws AuthorizationException
     */
    public function store(RateRequest $request): JsonResponse
    {
        try {
            $rate = $this->rateRepository->create(
                $request->validated() + ['agreement_id' => $request->agreement],
                fn($r) => $this->authorize('isStakeholder', $r->agreement)
            );

            return $this->responseService->sendMessage('Rate created', $rate->toArray(), 201);
        } catch (NotSavedModelException) {
            return $this->responseService->sendError('Rate not created');
        }
    }

    /**
     * @OA\Get(
     *     tags={"Rate"},
     *     path="/agreement/{agreement}/rate/{author}",
     *     summary="Fetch rate",
     *     description="Fetch unique rate of an agreement",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *     @OA\Parameter(ref="#/components/parameters/Agreement"),
     *     @OA\Parameter(ref="#/components/parameters/Author"),
     *     @OA\Response(response="200", ref="#/components/responses/ReturnRate"),
     * )
     *
     * @throws CheckDBOperationException
     * @throws AuthorizationException
     */
    public function show(RateRequest $request): JsonResponse
    {
        $rate = $this->rateRepository->fetch($request->author, $request->agreement);

        return $this->responseService->sendMessage(
            'Rate found',
            $rate->toArray()
        );
    }

    /**
     * @OA\Post(
     *     tags={"Rate"},
     *     path="/agreement/{agreement}/rate/{author}/update",
     *     summary="[PUT]::/agreement/{agreement}/rate/{author} alias",
     *     description="Redirect request to [PUT]::/agreement/{agreement}/rate/{author}",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\Parameter(ref="#/components/parameters/Agreement"),
     *     @OA\Parameter(ref="#/components/parameters/Author"),
     *     @OA\Response(response="302", description="Redirected to [PUT]::/agreement/{agreement}/rate/{author}")
     * )
     * @OA\Put(
     *     tags={"Rate"},
     *     path="/agreement/{agreement}/rate/{author}",
     *     summary="Update rate",
     *     description="Update rate on database",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *     @OA\Parameter(ref="#/components/parameters/Agreement"),
     *     @OA\RequestBody(ref="#/components/requestBodies/RateUpdate"),
     *     @OA\Response(response="200", ref="#/components/responses/ReturnRate"),
     * )
     *
     * @throws CheckDBOperationException
     * @throws AuthorizationException
     */
    public function update(RateRequest $request): JsonResponse
    {
        try {
            $rate = $this->rateRepository->update(
                $request->author,
                $request->agreement,
                $request->validated(),
                fn($r) => $this->authorize('isAdmin', $r->author)
            );

            return $this->responseService->sendMessage('Rate updated', $rate->toArray());
        } catch (NotSavedModelException) {
            return $this->responseService->sendError('Rate not updated');
        }
    }

    /**
     * @OA\Post(
     *      tags={"Rate"},
     *      path="/agreement/{agreement}/rate/{author}/delete",
     *      summary="[DELETE]::/agreement/{agreement}/rate/{author} alias",
     *      description="Redirect request to [DELETE]::/agreement/{agreement}/rate/{author}",
     *      security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *      @OA\Parameter(ref="#/components/parameters/Agreement"),
     *      @OA\Parameter(ref="#/components/parameters/Author"),
     *      @OA\Response(response="302", description="Redirected to [DELETE]::/agreement/{agreement}/rate/{author}")
     *  )
     * @OA\Delete(
     *     tags={"Rate"},
     *     path="/agreement/{agreement}/rate/{author}",
     *     summary="Delete rate",
     *     description="Delete rate on database",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *     @OA\Parameter(ref="#/components/parameters/Agreement"),
     *     @OA\Parameter(ref="#/components/parameters/Author"),
     *     @OA\Response(response="204", ref="#/components/responses/204"),
      * )
     *
     * @throws CheckDBOperationException
     * @throws AuthorizationException
     */
    public function destroy(RateRequest $request): JsonResponse
    {
        return $this->rateRepository->delete(
            $request->author,
            $request->agreement,
            fn($r) => $this->authorize('isAdmin', $r->author)
        ) ?
            $this->responseService->sendMessage("$request->author's rate has been deleted from the $request->agreement") :
            $this->responseService->sendError("$request->author's rate continues on the $request->agreement");
    }
}
