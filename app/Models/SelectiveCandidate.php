<?php

namespace App\Models;

use App\Exceptions\CheckDBOperationException;
use App\Models\Traits\HasHiddenTimestamps;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Thiagoprz\CompositeKey\HasCompositeKey;

class SelectiveCandidate extends Model
{
    use HasCompositeKey, HasFactory, HasHiddenTimestamps {
        HasHiddenTimestamps::__construct as hideTimestamps;
    }

    protected $primaryKey = ['artist_id', 'selective_id'];

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->hideTimestamps();
    }

    protected $fillable = ['artist_id', 'selective_id'];

    protected $hidden = ['artist_id', 'selective_id'];

    protected function artist(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Artist::class, 'id', 'id', 'artist_id');
    }

    protected function selective(): BelongsTo
    {
        return $this->belongsTo(Selective::class, 'selective_id');
    }

    public function withAllRelations(): SelectiveCandidate
    {
        return $this->load('artist', 'selective');
    }

    /**
     * @param array $options
     * @return bool
     * @throws CheckDBOperationException
     */
    public function save(array $options = []): bool
    {
        throw_unless(
            $this->artist->user->active,
            new CheckDBOperationException("The artist's account $this->artist_id is disabled")
        );

        $activeInterval = $this->selective->getActiveInterval();

        throw_unless(
            Carbon::now()->isBetween($activeInterval['start_moment'], $activeInterval['end_moment']),
            new CheckDBOperationException("The selective $this->selective_id is closed")
        );

        return parent::save($options);
    }
}
