<?php

namespace App\Repositories;

use App\Exceptions\CheckDBOperationException;
use App\Exceptions\HttpRequestException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotSavedModelException;
use App\Models\Art;
use App\Models\Selective;
use Carbon\Carbon;
use Closure;
use Ramsey\Collection\Collection;

class SelectiveRepository implements Contracts\ISelectiveRepository
{
    public function list(int $offset, int $limit): Collection|array
    {
        return Selective::withAllRelations()
            ->where('id', '>', $offset)
            ->where('end_moment', '>', Carbon::now())
            ->where(fn ($s) => $s->enterprise->active)
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

        if ($selective->enterprise->active) {
            return $selective;
        }
        CheckDBOperationException::throw("The enterprise's account $selective->enterprise_id is disabled");
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

        if (! $selective->enterprise->active) {
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

        throw_unless(
            $start->isFuture(),
            new CheckDBOperationException('The start_moment must be a future moment')
        );

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
