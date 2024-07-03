<?php

namespace App\Repositories;

use App\Exceptions\CheckDBOperationException;
use App\Exceptions\Contracts\HttpRequestException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotSavedModelException;
use App\Models\Art;
use App\Models\Selective;
use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Collection;

class SelectiveRepository implements Contracts\ISelectiveRepository
{
    public function list(int|string $offset, int|string $limit = 20): Collection|array
    {
        return Selective::withAllRelations()
            ->where('id', '>', $offset)
            ->where('end_moment', '>', Carbon::now())
            ->whereHas('enterprise', fn ($q) => $q->whereNull('deleted_at'))
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * @throws HttpRequestException
     * @throws CheckDBOperationException
     */
    public function fetch(int|string $id): Selective
    {
        $selective = Selective::findOr(
            $id,
            fn () => NotFoundException::throw("Selective $id was not found")
        )->loadAllRelations();

        throw_if($selective->enterprise->trashed(), CheckDBOperationException::class, "The enterprise's account $selective->enterprise_id is disabled");

        return $selective;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  CheckDBOperationException
     */
    public function create(array $data, Closure $validate): Selective
    {
        $selective = new Selective($data);
        $validate($selective);

        if (! $selective->enterprise->trashed()) {
            CheckDBOperationException::throw("The enterprise's account $selective->enterprise_id is disabled");
        }

        $selective->art_id = Art::where('name', $data['art'])->firstOrFail()->id;

        throw_unless($selective->save(), NotSavedModelException::class);

        return $selective->load('enterprise', 'art');
    }

    /**
     * {@inheritdoc}
     *
     * @throws HttpRequestException
     * @throws CheckDBOperationException
     */
    public function update(int|string $id, array $data, Closure $validate): Selective
    {
        $selective = $this->fetch($id);

        $validate($selective);

        [$start] = $selective->getActiveInterval();

        throw_unless($start->isFuture(), CheckDBOperationException::class, 'The start_moment must be a future moment');

        throw_unless($selective->update($data), NotSavedModelException::class);

        return $selective;
    }

    public function delete(int|string $id, Closure $validate): bool
    {
        $selective = $this->fetch($id);
        $validate($selective);

        return $selective->delete();
    }
}
