<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundRecordException;
use App\Http\Requests\SelectiveRequest;
use App\Models\Art as ModelsArt;
use App\Models\Selective;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Date;

class SelectiveController extends IController
{
    public function index(): JsonResponse|RedirectResponse
    {
        return $this->responseService->sendMessage(
            'Selectives found',
            Selective::with('enterprise', 'art')
                ->get()
                ->toArray()
        );
    }

    public function store(SelectiveRequest $request): JsonResponse|RedirectResponse
    {
        $selective = new Selective($request->validated());
        $selective->art_id = ModelsArt::where('name', $request->art)->first()->id;
        $selective->save();

        return $this->responseService->sendMessage('Selective created', $selective->load('art')->toArray());
    }

    /**
     * @throws NotFoundRecordException
     */
    protected function fetch(string $id): Model
    {
        return Selective::findOr($id, fn () => NotFoundRecordException::throw("Selective $id was not found"))->withAllRelations();
    }

    public function show(SelectiveRequest $request): JsonResponse|RedirectResponse
    {
        return $this->responseService->sendMessage(
            'Selective found',
            $this->fetch($request->id)
        );
    }

    public function update(SelectiveRequest $request): JsonResponse|RedirectResponse
    {
        $selective = $this->fetch($request->id);

        if (Date::createFromFormat('d/m/Y H:i', $selective->start_moment)->isAfter('now')) { // If the selective did not start
            $selective->fill($request->validated());

            return $selective->save() ?
                $this->responseService->sendMessage('Selective updated', $selective) :
                $this->responseService->sendError('Selective not updated');
        }

        return $this->responseService
            ->sendError(
                "It is not possible to update the selective $selective->id, as it started on $selective->start_moment"
            );
    }

    public function destroy(SelectiveRequest $request): JsonResponse|RedirectResponse
    {
        $selective = $this->fetch($request->id);

        return $selective->delete() ?
            $this->responseService->sendMessage('Selective deleted') :
            $this->responseService->sendError('Selective not deleted');
    }
}
