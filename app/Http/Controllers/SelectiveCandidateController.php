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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SelectiveCandidateController extends ISubController
{
    public function index(Request $request): JsonResponse|RedirectResponse
    {
        return $this->responseService->sendMessage(
            'List',
            Selective::find($request->selective)
                ->candidates
                ->load('user')
                ->toArray()
        );
    }

    /**
     * @throws NotFoundRecordException
     */
    protected function fetch(string $serviceId, string $userId): Model
    {
        return SelectiveCandidate::findOr([$userId, $serviceId], fn () => NotFoundRecordException::throw("Candidate $userId was not found on selective $serviceId"));
    }

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
