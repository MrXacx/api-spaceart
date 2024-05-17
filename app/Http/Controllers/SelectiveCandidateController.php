<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundRecordException;
use App\Exceptions\NotSavedModelException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Requests\SelectiveCandidateRequest;
use App\Models\Selective;
use App\Models\SelectiveCandidate;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class SelectiveCandidateController extends ISubController
{
    protected function store(SelectiveCandidateRequest $request): JsonResponse|RedirectResponse
    {
        $candidature = new SelectiveCandidate($request->validated() + ['selective_id' => $request->selective]);

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
