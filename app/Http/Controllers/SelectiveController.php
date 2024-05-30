<?php

namespace App\Http\Controllers;

use App\Exceptions\NotSavedModelException;
use App\Http\Controllers\Contracts\IMainRouteController;
use App\Http\Requests\SelectiveRequest;
use App\Repositories\SelectiveRepository;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ControllerMiddlewareOptions;

class SelectiveController extends IMainRouteController
{
    public function __construct(
        private readonly SelectiveRepository $selectiveRepository,
        ResponseService $responseService
    ) {
        parent::__construct($responseService);
    }

    protected function setSanctumMiddleware(): ControllerMiddlewareOptions
    {
        return parent::setSanctumMiddleware()->except('index');
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => ['numeric', 'min:1', 'max:100', 'nullable'],
            'offset' => ['numeric', 'min:1', 'nullable'],
        ]);

        return $this->responseService->sendMessage(
            'Selectives found',
            $this->selectiveRepository->list($request->offset ?? 0, $request->limit ?? 20)
        );
    }

    public function store(SelectiveRequest $request): JsonResponse
    {
        try {
            $selective = $this->selectiveRepository->create(
                $request->validated(),
                fn ($s) => $this->authorize('isOwner', $s)
            );

            return $this->responseService->sendMessage('Selective was created', $selective->toArray(), 201);
        } catch (NotSavedModelException) {
            return $this->responseService->sendError('Selective was not created');
        }
    }

    public function show(SelectiveRequest $request): JsonResponse
    {
        return $this->responseService->sendMessage(
            "Selective $request->id found",
            $this->selectiveRepository->fetch($request->id)
        );
    }

    public function update(SelectiveRequest $request): JsonResponse
    {
        try {
            $selective = $this->selectiveRepository->update(
                $request->validated(),
                fn ($s) => $this->authorize('isOwner', $s)
            );

            return $this->responseService->sendMessage('Selective updated', $selective);
        } catch (NotSavedModelException) {
            return $this->responseService->sendError('Selective not updated');
        }
    }

    public function destroy(SelectiveRequest $request): JsonResponse
    {
        return $this->selectiveRepository->delete(
            $request->id,
            fn ($s) => $this->authorize('isAdmin', $s->enterprise)
        ) ?
            $this->responseService->sendMessage('Selective deleted', 204) :
            $this->responseService->sendError('Selective not deleted');
    }
}
