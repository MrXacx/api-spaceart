<?php

namespace App\Repositories;

use App\Exceptions\DatabaseValidationException;
use App\Exceptions\NotFoundModelException;
use App\Exceptions\NotSavedModelException;
use App\Exceptions\TrashedModelReferenceException;
use App\Models\Art;
use App\Models\Selective;
use Closure;
use Illuminate\Database\Eloquent\Collection;

class SelectiveRepository implements Contracts\ISelectiveRepository
{
    public function list(int|string $offset, int|string $limit = 20): Collection|array
    {
        return Selective::withAllRelations()
            ->where('id', '>', $offset)
            ->where('end_moment', '>', now())
            ->whereHas('enterprise', fn ($q) => $q->whereNull('deleted_at'))
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * @throws TrashedModelReferenceException
     * @throws DatabaseValidationException
     */
    public function fetch(int|string $id): Selective
    {
        $selective = Selective::findOr(
            $id,
            fn () => NotFoundModelException::throw("Selective $id was not found")
        )->loadAllRelations();

        throw_if($selective->enterprise->trashed(), TrashedModelReferenceException::class, "The enterprise's account $selective->enterprise_id is disabled");

        return $selective;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  DatabaseValidationException
     */
    public function create(array $data, Closure $validate): Selective
    {
        $selective = new Selective($data);
        $validate($selective);

        if ($selective->enterprise->trashed()) {
            TrashedModelReferenceException::throw("The enterprise's account $selective->enterprise_id is disabled");
        }

        $selective->art_id = Art::where('name', $data['art'])->firstOrFail()->id;

        throw_unless($selective->save(), NotSavedModelException::class);

        return $selective->load('enterprise', 'art');
    }

    /**
     * {@inheritdoc}
     *
     * @throws TrashedModelReferenceException
     * @throws DatabaseValidationException
     */
    public function update(int|string $id, array $data, Closure $validate): Selective
    {
        $selective = $this->fetch($id);

        $validate($selective);

        ['start_moment' => $start] = $selective->getActiveInterval();

        throw_unless($start->isFuture(), DatabaseValidationException::class, 'The start moment must be a future moment');

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
