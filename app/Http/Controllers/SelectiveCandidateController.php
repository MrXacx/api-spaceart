<?php

namespace App\Http\Controllers;

use App\Exceptions\CheckDBOperationException;
use App\Exceptions\NotSavedModelException;
use App\Http\Controllers\Contracts\IRouteController;
use App\Http\Requests\SelectiveCandidateRequest;
use App\Repositories\SelectiveCandidateRepository;
use App\Services\ResponseService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class SelectiveCandidateController extends IRouteController
{
    public function __construct(ResponseService $responseService,
        private readonly SelectiveCandidateRepository $selectiveCandidateRepository,
    ) {
        parent::__construct($responseService);
    }

    /**
     * @OA\Post(
     *     tags={"Candidate"},
     *     path="/selective/{selective}/candidate",
     *     summary="Store candidature",
     *     description="Strore candidature on selective",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\Parameter(name="selective", in="path", description="Selective id", style="form", @OA\Schema(type="integer")),
     *     @OA\RequestBody(ref="#/components/requestBodies/SelectiveCandidateStore"),
     *     @OA\Response(
     *         response="201",
     *         description="Candidature finished successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", default="Candidature was created"),
     *             @OA\Property(property="data", type="array", @OA\Items(maxItems=1, ref="#/components/schemas/SelectiveCandidate")),
     *             @OA\Property(property="fails", type="boolean", default="false"),
     *         )
     *     ),
     * )
     *
     * @throws CheckDBOperationException
     * @throws AuthorizationException
     */
    protected function store(SelectiveCandidateRequest $request): JsonResponse
    {
        $data = $request->validated() + ['selective_id' => $request->selective];

        try {
            $candidature = $this->selectiveCandidateRepository
                ->create(
                    $data,
                    fn ($c) => $this->authorize('isAdmin', $c->artist)
                );

            return $this->responseService->sendMessage('Candidature created', $candidature->toArray(), 201);
        } catch (NotSavedModelException) {
            return $this->responseService->sendError('Candidature not created');
        }
    }
}
