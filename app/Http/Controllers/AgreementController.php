<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundRecordException;
use App\Exceptions\NotSavedModelException;
use App\Http\Requests\AgreementRequest;
use App\Models\Agreement;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AgreementController extends IController
{
    public function index(): JsonResponse|RedirectResponse
    {
        return $this->responseService->sendMessage(
            'Agreements found',
            Agreement::with('art', 'artist', 'enterprise')
                ->get()
                ->toArray()
        );
    }

    public function store(AgreementRequest $request): JsonResponse|RedirectResponse
    {
        $agreement = new Agreement($request->validated());
        $agreement->art_id = $agreement->artist->art_id;

        try {
            throw_unless($agreement->save(), NotSavedModelException::class);
            return $this->responseService->sendMessage('Agreement created', $agreement->toArray());
        } catch (Exception $e) {
            return $this->responseService->sendError('Agreement not created', [$e->getMessage()]);
        }
    }

    /**
     * @throws NotFoundRecordException
     */
    protected function fetch(string $id): Model
    {
        return Agreement::findOr($id, fn () => NotFoundRecordException::throw("Agreement $id was not found"))->withAllRelations();
    }

    public function show(AgreementRequest $request): JsonResponse//: JsonResponse|RedirectResponse
    {
        return $this->responseService->sendMessage(
            'Agreement found',
            $this->fetch($request->id)->toArray()
        );
    }

    public function update(AgreementRequest $request): JsonResponse|RedirectResponse
    {
        $agreement = $this->fetch($request->id)->fill($request->validated());

        try {
            throw_unless($agreement->save(), NotSavedModelException::class);

            return $this->responseService->sendMessage('Agreement updated', $agreement->toArray());
        } catch (Exception $e) {
            return $this->responseService->sendError('Agreement not updated', [$e->getMessage()]);
        }
    }

    public function destroy(AgreementRequest $request): JsonResponse|RedirectResponse
    {
        $agreement = $this->fetch($request->id);

        return $agreement->delete() ?
            $this->responseService->sendMessage('Agreement deleted') :
            $this->responseService->sendError('Agreement not deleted');
    }
}
