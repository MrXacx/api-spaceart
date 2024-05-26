<?php

namespace App\Models;

use App\Exceptions\CheckDBOperationException;
use App\Models\Traits\HasHiddenTimestamps;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use OpenApi\Annotations as OA;
use Thiagoprz\CompositeKey\HasCompositeKey;

/**
 * @OA\Schema(
 *     schema="SelectiveCandidate",
 *     description="Schema of Selective Candidate",
 *
 *     @OA\Property(property="artist", ref="#/components/schemas/User"),
 *     @OA\Property(property="selective", ref="#/components/schemas/Selective"),
 * )
 */
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

    public function loadAllRelations(): SelectiveCandidate
    {
        return $this->load('artist', 'selective');
    }

    public static function withAllRelations(): Builder
    {
        return static::with('artist', 'selective');
    }

    /**
     * @throws CheckDBOperationException
     */
    public function save(array $options = []): bool
    {
        throw_unless(
            $this->artist->active,
            new CheckDBOperationException("The artist's account $this->artist_id is disabled")
        );

        if (app()->isProduction()) {
            throw_unless(
                $this->artist->artistAccountData->art === $this->selective->art,
                new CheckDBOperationException("The {$this->artist->artistAccountData->art->name} account is not able to {$this->selective->art->name} selective")
            );

            $activeInterval = $this->selective->getActiveInterval();

            throw_unless(
                Carbon::now()->isBetween($activeInterval['start_moment'], $activeInterval['end_moment']),
                new CheckDBOperationException("The selective $this->selective_id is closed")
            );
        }

        return parent::save($options);
    }
}
