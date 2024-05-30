<?php

namespace App\Http\Controllers;

use App\Exceptions\NotSavedModelException;
use App\Http\Requests\AgreementRequest;
use App\Repositories\AgreementRepository;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgreementController extends IMainRouteController
{
    public function __construct(
        ResponseService $responseService,
        private readonly AgreementRepository $agreementRepository
    ) {
        parent::__construct($responseService);
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate(['limit' => ['numeric', 'min:1', 'max:100', 'nullable']]);

        return $this->responseService->sendMessage(
            'Agreements found',
            $this->agreementRepository->list()->toArray()
        );
    }

    public function store(AgreementRequest $request): JsonResponse
    {
        try {
            $agreement = $this->agreementRepository->create(
                $request->id,
                fn ($a) => $this->authorize('isStakeholder', $a)
            );

            return $this->responseService->sendMessage('Agreement created', $agreement->toArray(), 201);
        } catch (NotSavedModelException) {
            return $this->responseService->sendError('Agreement not created');
        }
    }

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

    public function destroy(AgreementRequest $request): JsonResponse
    {
        return $this->agreementRepository->delete($request->id, fn ($a) => $this->authorize('isStakeholder', $a)) ?
            $this->responseService->sendMessage('Agreement deleted', 204) :
            $this->responseService->sendError('Agreement not deleted');
    }
}
