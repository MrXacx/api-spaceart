<?php

namespace App\Http\Controllers;

use App\Exceptions\CheckDBOperationException;
use App\Exceptions\NotSavedModelException;
use App\Http\Controllers\Contracts\IMainRouteController;
use App\Http\Requests\AgreementRequest;
use App\Repositories\AgreementRepository;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Response(
 *  response="ReturnAgreement",
 *  description="Response is successful",
 *
 *  @OA\JsonContent(
 *
 *       @OA\Property(property="message", type="string"),
 *       @OA\Property(property="data", type="array", maxItems=1, @OA\Items(ref="#/components/schemas/Agreement")),
 *       @OA\Property(property="fails", type="bool"),
 *   )
 * ),
 */
class AgreementController extends IMainRouteController
{
    private readonly AgreementRepository $agreementRepository;

    public function __construct(
        ResponseService $responseService,
        AgreementRepository $agreementRepository
    ) {
        $this->agreementRepository = $agreementRepository;
        parent::__construct($responseService);
    }

    /**
     * @OA\Get(
     *     tags={"Agreement"},
     *     path="/agreement",
     *     summary="List agreements",
     *     description="Fetch user's agreements on database",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\Parameter(ref="#/components/parameters/Limit"),
     *
     *     @OA\Response(
     *         response="200",
     *          description="Agreements found",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Agreement")),
     *              @OA\Property(property="fails", type="bool"),
     *          )
     *     ),
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate(['limit' => ['numeric', 'min:1', 'max:100', 'nullable']]);

        return $this->responseService->sendMessage(
            'Agreements found',
            $this->agreementRepository->list(auth()->id(), $request->limit ?? 20)->toArray()
        );
    }

    /**
     * @OA\Post(
     *     tags={"Agreement"},
     *     path="/agreement",
     *     summary="Store agreement",
     *     description="Store agreement on database",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\RequestBody(ref="#/components/requestBodies/AgreementStore"),
     *
     *     @OA\Response(
     *         response="200",
     *          description="Agreements was created",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="array", maxItems=1, @OA\Items(ref="#/components/schemas/Agreement")),
     *              @OA\Property(property="fails", type="bool"),
     *          )
     *     ),
     *
     * )
     */
    public function store(AgreementRequest $request): JsonResponse
    {
        try {
            $agreement = $this->agreementRepository->create(
                $request->validated(),
                fn ($a) => $this->authorize('isStakeholder', $a)
            );

            return $this->responseService->sendMessage('Agreement created', $agreement->toArray(), 201);
        } catch (NotSavedModelException) {
            return $this->responseService->sendError('Agreement not created');
        }
    }

    /**
     * @OA\Get(
     *     tags={"Agreement"},
     *     path="/agreement/{id}",
     *     summary="Fetch agreement",
     *     description="Fetch an unique agreement on database",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\Parameter(ref="#/components/parameters/Id"),
     *
     *     @OA\Response(response="200", ref="#/components/responses/ReturnAgreement"),
     *
     * )
     */
    public function show(AgreementRequest $request): JsonResponse//: JsonResponse
    {
        $agreement = $this->agreementRepository->fetch(
            $request->id,
            fn ($a) => $this->authorize('isStakeholder', $a)
        );

        return $this->responseService->sendMessage(
            "Agreement $request->id found",
            $agreement->toArray()
        );
    }

    /**
     * @OA\Post(
     *     tags={"Agreement"},
     *     path="/agreement/{id}/update",
     *     summary="[PUT]::/agreement/{id} alias",
     *     description="Redirect request to [PUT]::/agreement/{id} alias",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\Parameter(ref="#/components/parameters/Id"),
     *
     *     @OA\Response(response="302", description="Redirected to [PUT]::/agreement/{id}")
     * )
     *
     * @OA\Put(
     *     tags={"Agreement"},
     *     path="/agreement/{id}",
     *     summary="Update agreement",
     *     description="Update agreement on database",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\Parameter(ref="#/components/parameters/Id"),
     *
     *     @OA\RequestBody(ref="#/components/requestBodies/AgreementUpdate"),
     *
     *     @OA\Response(response="200", ref="#/components/responses/ReturnAgreement"),
     * )
     *
     * @throws CheckDBOperationException
     */
    public function update(AgreementRequest $request): JsonResponse
    {
        $validate = count($request->validated()) > 1 ?
        fn ($a) => $this->authorize('isHirer', $a) :
        fn ($a) => $this->authorize('isStakeholder', $a);

        try {
            $agreement = $this->agreementRepository->update($request->id, $request->validated(), $validate);

            return $this->responseService->sendMessage('Agreement updated', $agreement->toArray());
        } catch (NotSavedModelException) {
            return $this->responseService->sendError('Agreement not updated');
        }
    }

    /**
     * @OA\Post(
     *     tags={"Agreement"},
     *     path="/agreement/{id}/delete",
     *     summary="[DELETE]::/agreement/{id} alias",
     *     description="Redirect request to [DELETE]::/agreement/{id} alias",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\Parameter(ref="#/components/parameters/Id"),
     *
     *     @OA\Response(response="302", description="Redirected to [DELETE]::/agreement/{id}")
     * )
     *
     * @OA\Delete(
     *     tags={"Agreement"},
     *     path="/agreement/{id}",
     *     summary="Delete agreement",
     *     description="Delete agreement on database",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\Parameter(ref="#/components/parameters/Id"),
     *
     *     @OA\Response(response="204", ref="#/components/responses/204"),
     * )
     */
    public function destroy(AgreementRequest $request): JsonResponse
    {
        return $this->agreementRepository->delete($request->id, fn ($a) => $this->authorize('isStakeholder', $a)) ?
            $this->responseService->sendMessage('Agreement deleted', 204) :
            $this->responseService->sendError('Agreement not deleted');
    }
}
