<?php

namespace App\Http\Controllers;


use App\Models\Rate;
use App\Enumerate\Account;
use App\Repositories\RateRepository;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\RateRequest;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotSavedModelException;

use function PHPUnit\Framework\returnSelf;
use App\Exceptions\NotFoundException;

class RateController extends IRouteController
{
    public function __construct(
        ResponseService $responseService,
        private readonly RateRepository $rateRepository
    )
    {
        parent::__construct($responseService);
    }

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

    public function show(RateRequest $request): JsonResponse
    {
        $rate = $this->rateRepository->fetch($request->author, $request->agreement);

        return $this->responseService->sendMessage(
            'Rate found',
            $rate->toArray()
        );
    }

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
