<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundRecordException;
use App\Exceptions\NotSavedModelException;
use App\Http\Requests\SelectiveRequest;
use App\Models\Art as ModelsArt;
use App\Models\Selective;
use Carbon\Carbon;
use Enumerate\TimeStringFormat;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use UnexpectedValueException;

class SelectiveController extends IMainRouteController
{
    protected function setSanctumMiddleware(): \Illuminate\Routing\ControllerMiddlewareOptions
    {
        return parent::setSanctumMiddleware()->except('index');
    }

    public function index(): JsonResponse
    {
        return $this->responseService->sendMessage(
            'Selectives found',
            Selective::with('enterprise', 'art')
                ->where('end_moment', '>', Carbon::now())
                ->get()
                ->toArray()
        );
    }

    public function store(SelectiveRequest $request): JsonResponse
    {
        $selective = new Selective($request->validated());
        $selective->art_id = ModelsArt::where('name', $request->art)->firstOrFail()->id;
        try {
            throw_unless($selective->save(), NotSavedModelException::class);

            return $this->responseService->sendMessage('Selective created', $selective->load('art')->toArray());
        } catch (Exception $e) {
            return $this->responseService->sendError('Selective not created', [$e->getMessage()]);
        }
    }

    /**
     * @throws NotFoundRecordException
     */
    protected function fetch(string $id): Model
    {
        return Selective::findOr(
            $id,
            fn() => NotFoundRecordException::throw("Selective $id was not found")
        )->withAllRelations();
    }

    public function show(SelectiveRequest $request): JsonResponse
    {
        return $this->responseService->sendMessage(
            'Selective found',
            $this->fetch($request->id)
        );
    }

    public function update(SelectiveRequest $request): JsonResponse
    {
        try {
            $selective = $this->fetch($request->id);
            $this->authorize('isOwner', $selective);
            throw_unless(
                Carbon::createFromFormat(TimeStringFormat::DATE_TIME_FORMAT->value, $selective->start_moment)
                    ->isFuture(),
                new UnexpectedValueException('The start_moment must be a future moment')
            );
            $selective->fill($request->validated());

            throw_unless($selective->save(), NotSavedModelException::class);

            return $this->responseService->sendMessage('Selective updated', $selective);
        } catch (Exception $e) {
            return $this->responseService->sendError('Selective not updated', [$e->getMessage()]);
        }
    }

    public function destroy(SelectiveRequest $request): JsonResponse
    {
        $selective = $this->fetch($request->id);
        $this->authorize('isOwner', $selective);
        return $selective->delete() ?
            $this->responseService->sendMessage('Selective deleted') :
            $this->responseService->sendError('Selective not deleted');
    }
}
