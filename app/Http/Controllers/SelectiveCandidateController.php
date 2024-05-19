<?php

namespace App\Http\Controllers;

use App\Exceptions\NotSavedModelException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Requests\SelectiveCandidateRequest;
use App\Models\SelectiveCandidate;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;

class SelectiveCandidateController extends IRouteController
{
    protected function store(SelectiveCandidateRequest $request): JsonResponse
    {
        $candidature = new SelectiveCandidate($request->validated() + ['selective_id' => $request->selective]);
        $this->authorize('isCandidate', $candidature);
        $activeInterval = $candidature->selective->getActiveInterval();
        try {
            throw_unless(
                Carbon::now()->isBetween($activeInterval['start_moment'], $activeInterval['end_moment']),
                new UnprocessableEntityException("selective $request->selective is closed")
            );
            throw_unless($candidature->save(), NotSavedModelException::class);

            return $this->responseService->sendMessage('Candidature created', $candidature->toArray());
        } catch (Exception $e) {
            return $this->responseService->sendError('Candidature not created', [$e->getMessage()]);
        }
    }
}
