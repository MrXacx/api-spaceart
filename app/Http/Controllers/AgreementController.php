<?php

namespace App\Http\Controllers;

use App\Models\Art;
use App\Models\User;
use App\Models\Artist;
use App\Models\Agreement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\AgreementRequest;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotFoundRecordException;

class AgreementController extends IController
{
    public function index(): JsonResponse|RedirectResponse
    {
        return $this->responseService->sendMessage(
            'Lista de usuário encontrada',
            Agreement::with('art', 'artist', 'enterprise')
                ->get()
                ->toArray()
        );
    }

    public function store(AgreementRequest $request): JsonResponse|RedirectResponse
    {
        $agreement = new Agreement($request->validated());
        $agreement->art_id = $agreement->artist->art_id;
        $agreement->save();

        return $this->responseService->sendMessage('Agreement created', $agreement->toArray());
    }

    protected function fetch(string $id, array $options = []): Model
    {
        return Agreement::findOr($id, fn() => NotFoundRecordException::throw("Agreement $id was not found"));
    }

    public function show(AgreementRequest $request)//: JsonResponse|RedirectResponse
    {
        return $this->responseService->sendMessage(
            'Agreement found',
            $this->fetch($request->id)
        );
    }

    public function update(AgreementRequest $request): JsonResponse|RedirectResponse
    {
        $agreement = $this->fetch($request->id);
        $agreement->fill($request->validated());

        return $agreement->save() ?
            $this->responseService->sendMessage('Agreement updated', $agreement) :
            $this->responseService->sendError('Agreement not updated');
    }

    public function destroy(AgreementRequest $request): JsonResponse|RedirectResponse
    {
        $agreement = $this->fetch($request->id);

        return $agreement->delete() ?
            $this->responseService->sendMessage('Agreement deleted') :
            $this->responseService->sendError('Agreement not deleted');
    }
}
