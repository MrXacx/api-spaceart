<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundRecordException;
use App\Exceptions\NotSavedModelException;
use App\Http\Requests\AgreementRequest;
use App\Models\Agreement;
use Enumerate\AgreementStatus;
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
                ->where('artist_id', '=', auth()->id())
                //->orWhere('enterprise_id', '=', auth()->id(), 'or')
                ->get()
                ->toArray()
        );
    }

    public function store(AgreementRequest $request): JsonResponse|RedirectResponse
    {
        $agreement = new Agreement($request->validated());
        $agreement->art_id = $agreement->artist->artistAccountData->art_id;

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
        $agreement = $this->fetch($request->id);
        $this->authorize('isStakeholder', $agreement);

        return $this->responseService->sendMessage(
            'Agreement found',
            $agreement->toArray()
        );
    }

    public function update(AgreementRequest $request): JsonResponse|RedirectResponse
    {
        $agreementData = $request->validated();
        $agreement = $this->fetch($request->id);
        if (count($agreementData) > 1) { // If exists some key different of status
            $this->authorize('isEnterprise', $agreement);
            $agreementData['status'] = AgreementStatus::SEND->value; // Change agreement status to 'send' if agreement conditions have changed
        } else {
            $this->authorize('isStakeholder', $agreement);
        }

        $agreement->fill($agreementData);

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
        $this->authorize('isStakeholder', $agreement);

        return $agreement->delete() ?
            $this->responseService->sendMessage('Agreement deleted') :
            $this->responseService->sendError('Agreement not deleted');
    }
}
