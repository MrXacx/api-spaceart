<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundRecordException;
use App\Http\Requests\SelectiveCandidateRequest;
use App\Models\Selective;
use App\Models\SelectiveCandidate;
use Carbon\Carbon;
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

        return
            Carbon::now('America/Sao_Paulo')
                ->isBetween($activeInterval['start_moment'], $activeInterval['end_moment']) &&
            $candidature->save() ?
                $this->responseService->sendMessage('Candidature created', $candidature->toArray()) :
                $this->responseService->sendError('Candidature not created', ["selective $request->selective is closed"]);
    }
}
