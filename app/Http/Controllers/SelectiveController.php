<?php

namespace App\Http\Controllers;


use Exception;
use Carbon\Carbon;
use App\Models\Selective;
use Illuminate\Http\Request;
use UnexpectedValueException;
use App\Models\Art as ModelsArt;
use Illuminate\Http\JsonResponse;
use App\Enumerate\TimeStringFormat;
use App\Http\Requests\SelectiveRequest;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotSavedModelException;
use App\Exceptions\NotFoundException;
use Illuminate\Routing\ControllerMiddlewareOptions;

class SelectiveController extends IMainRouteController
{
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
            Selective::withAllRelations()
                ->where('end_moment', '>', Carbon::now())
                ->offset($request->offset ?? 0)
                ->limit($request->limit ?? 20)
                ->inRandomOrder()
                ->get()
                ->random()
                ->toArray()
        );
    }

    public function store(SelectiveRequest $request): JsonResponse
    {
        $selective = new Selective($request->validated());
        $this->authorize('isOwner', $selective);
        $selective->art_id = ModelsArt::where('name', $request->art)->firstOrFail()->id;
        try {
            throw_unless($selective->save(), NotSavedModelException::class);

            return $this->responseService->sendMessage('Selective created', $selective->loadAllRelations()->toArray(), 201);
        } catch (Exception $e) {
            return $this->responseService->sendError('Selective not created', [$e->getMessage()]);
        }
    }

    /**
     * @throws NotFoundException
     */
    protected function fetch(string|int $id): Model
    {
        return Selective::findOr(
            $id,
            fn() => NotFoundException::throw("Selective $id was not found")
        )->loadAllRelations();
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
