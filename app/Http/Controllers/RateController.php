<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundRecordException;
use App\Exceptions\NotSavedModelException;
use App\Http\Requests\RateRequest;
use App\Models\Rate;
use App\Enumerate\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class RateController extends IRouteController
{
    public function store(RateRequest $request): JsonResponse
    {
        $rate = new Rate($request->validated() + ['agreement_id' => $request->agreement]);
        $this->authorize('isStakeholder', $rate->agreement);

        $rate->rated_id = ($rate->author->type == Account::ARTIST ?
            $rate->agreement->enterprise :
            $rate->agreement->artist)->id;

        try {
            throw_unless($rate->save(), NotSavedModelException::class);

            return $this->responseService->sendMessage('Rate created', $rate->loadAllRelations()->toArray());
        } catch (\Exception $e) {
            return $this->responseService->sendError('Rate not created', [$e->getMessage()]);
        }
    }

    /**
     * @throws NotFoundRecordException
     */
    protected function fetch(string $serviceId, string $userId): Model
    {
        $rate = Rate::find([$userId, $serviceId]);

        return $rate ? $rate->loadAllRelations() : NotFoundRecordException::throw("user $userId's rate was not found on agreement $serviceId");
    }

    public function show(RateRequest $request): JsonResponse
    {
        $rate = $this->fetch($request->agreement, $request->author);

        return $this->responseService->sendMessage(
            'Rate found',
            $rate->loadAllRelations()->toArray()
        );
    }

    public function update(RateRequest $request): JsonResponse
    {
        $rate = $this->fetch($request->agreement, $request->author);
        $this->authorize('isAuthor', $rate);

        $rate->fill($request->validated());

        try {
            throw_unless($rate->save(), NotSavedModelException::class);

            return $this->responseService->sendMessage('Rate updated', $rate->toArray());
        } catch (\Exception $e) {
            return $this->responseService->sendError('Rate not updated', [$e->getMessage()]);
        }
    }

    public function destroy(
        RateRequest $request
    ): JsonResponse {
        $rate = $this->fetch($request->agreement, $request->author);
        $this->authorize('isAuthor', $rate);

        return $rate->delete() ?
            $this->responseService->sendMessage("$request->author's rate has been deleted from the $request->agreement") :
            $this->responseService->sendError("$request->author's rate continues on the $request->agreement");
    }
}
