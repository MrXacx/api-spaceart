<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundRecordException;
use App\Http\Requests\RateRequest;
use App\Models\Rate;
use Enumerate\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RateController extends ISubController
{
    public function index(Request $request): JsonResponse|RedirectResponse
    {
        return $this->responseService->sendMessage(
            'Rates found',
            Rate::with('author', 'rated', 'agreement')
                ->where('agreement_id', '=', $request->agreement)
                ->get()
                ->toArray()
        );
    }

    public function store(RateRequest $request): JsonResponse|RedirectResponse
    {
        $rate = new Rate($request->validated() + ['agreement_id' => $request->agreement]);
        $agreement = $rate->agreement;

        $ratedUser = ($rate->author->type == Account::ARTIST ? $agreement->enterprise : $agreement->artist)->user;
        $rate->rated_id = $ratedUser->id;

        $ratedUser->receivedRates->add($rate);
        $ratedUser->avg_rate = $ratedUser->receivedRates->average(fn ($r) => $r->score);

        DB::beginTransaction();
        if ($rate->save() && $ratedUser->save()) { // Executa se as queries forem funcionarem
            DB::commit(); // Confirma a persistência dos dados

            return $this->responseService->sendMessage('Rate created', $rate->load('author', 'rated', 'agreement')->toArray());
        }
        DB::rollBack(); // Reverte as inserções em caso de erro falha numa das queries

        return $this->responseService->sendError('Rate not created');
    }

    /**
     * @throws NotFoundRecordException
     */
    protected function fetch(string $serviceId, string $userId): Model
    {
        $rate = Rate::find([$userId, $serviceId]);
        return $rate ? $rate->withAllRelations() : NotFoundRecordException::throw("user $userId's rate was not found on agreement $serviceId");
    }

    public function show(RateRequest $request): JsonResponse|RedirectResponse
    {
        return $this->responseService->sendMessage(
            'Rate found',
            $this->fetch($request->agreement, $request->author)->toArray()
        );
    }

    public function update(RateRequest $request): JsonResponse|RedirectResponse
    {
        $rate = $this->fetch($request->agreement, $request->author);
        $ratedUser = $rate->rated;

        $rate->fill($request->validated());

        DB::beginTransaction();
        if ($rate->save()) {
            $ratedUser->avg_rate = $ratedUser->receivedRates->average('score');
            if ($ratedUser->save()) {
                DB::commit();

                return $this->responseService->sendMessage('Rate updated', $rate->toArray());
            }
        }
        DB::rollBack();

        return $this->responseService->sendError('Rate not updated');
    }

    public function destroy(RateRequest $request): JsonResponse|RedirectResponse
    {
        $rate = $this->fetch($request->agreement, $request->author);

        return $rate->delete() ?
            $this->responseService->sendMessage("$request->author's rate has been deleted from the $request->agreement") :
            $this->responseService->sendError("$request->author's rate continues on the $request->agreement");
    }
}
