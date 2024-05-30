<?php

namespace App\Http\Controllers;

use App\Exceptions\NotSavedModelException;
use App\Http\Controllers\Contracts\IRouteController;
use App\Http\Requests\SelectiveCandidateRequest;
use App\Repositories\SelectiveCandidateRepository;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

class SelectiveCandidateController extends IRouteController
{
    public function __construct(ResponseService $responseService,
        private readonly SelectiveCandidateRepository $selectiveCandidateRepository,
    ) {
        parent::__construct($responseService);
    }

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
