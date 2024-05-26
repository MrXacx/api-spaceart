<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundRecordException;
use App\Exceptions\NotSavedModelException;
use App\Http\Requests\AgreementRequest;
use App\Models\Agreement;
use App\Enumerate\AgreementStatus;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgreementController extends IMainRouteController
{
    public function index(Request $request): JsonResponse
    {
        $request->validate(['limit' => ['numeric', 'min:1', 'max:100', 'nullable']]);
        $id = $request->user()->id;
        return $this->responseService->sendMessage(
            'Agreements found',
            Agreement::withAllRelations()
                ->where('artist_id', '=', $id)
                ->orWhere('enterprise_id', '=', $id)
                ->limit($request->limit ?? 20)
                ->get()
                ->toArray()
        );
    }

    public function store(AgreementRequest $request): JsonResponse
    {
        $agreement = new Agreement($request->validated());
        $this->authorize('isStakeholder', $agreement);
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
    protected function fetch(string|int $id): Model
    {
        return Agreement::findOr($id, fn() => NotFoundRecordException::throw("Agreement $id was not found"))->loadAllRelations();
    }

    public function show(AgreementRequest $request): JsonResponse//: JsonResponse
    {
        $agreement = $this->fetch($request->id);
        $this->authorize('isStakeholder', $agreement);

        return $this->responseService->sendMessage(
            'Agreement found',
            $agreement->toArray()
        );
    }

    public function update(AgreementRequest $request): JsonResponse
    {
        $agreementData = $request->validated();
        $agreement = $this->fetch($request->id);
        if (count($agreementData) > 1) { // If exists some key different of status
            $this->authorize('isHirer', $agreement);
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

    public function destroy(AgreementRequest $request): JsonResponse
    {
        $agreement = $this->fetch($request->id);
        $this->authorize('isStakeholder', $agreement);

        return $agreement->delete() ?
            $this->responseService->sendMessage('Agreement deleted') :
            $this->responseService->sendError('Agreement not deleted');
    }
}
