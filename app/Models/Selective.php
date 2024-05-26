<?php

namespace App\Models;

use App\Exceptions\CheckDBOperationException;
use App\Models\Traits\HasDatetimeAccessorAndMutator;
use App\Models\Traits\HasHiddenTimestamps;
use Enumerate\TimeStringFormat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Selective",
 *     description="Schema of Model Selective",
 *
 *     @OA\Property(property="id", type="int"),
 *     @OA\Property(property="enterprise", ref="#/components/schemas/Enterprise"),
 *     @OA\Property(property="art", ref="#/components/schemas/Art"),
 *     @OA\Property(property="title", type="string", description="Comercial name of selective", example="Summer 2024's selective"),
 *     @OA\Property(property="note", type="string",  description="Description of service", example="Rock musical show for 2 hours"),
 *     @OA\Property(property="start_moment", type="date", description="Moment of open selective", example="01/01/2025 00:00"),
 *     @OA\Property(property="end_moment", type="date", description="Moment of open selective", example="31/12/2025 23:59"),
 *     @OA\Property(property="price", type="float", description="Value of fee", example="1000.00"),
 *     @OA\Property(property="candidates", type="array", @OA\Items(ref="#/components/schemas/SelectiveCandidate")),
 * )
 */
class Selective extends Model
{
    use HasDatetimeAccessorAndMutator, HasFactory, HasHiddenTimestamps {
        HasHiddenTimestamps::__construct as hideTimestamps;
    }

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->hideTimestamps();
    }

    protected $fillable = [
        'title',
        'enterprise_id',
        'start_moment',
        'end_moment',
        'art_id',
        'note',
        'price',
    ];

    protected $hidden = [
        'enterprise_id',
        'art_id',
        'laravel_through_key',
    ];

    public function enterprise(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Enterprise::class, 'id', 'id', 'enterprise_id');
    }

    public function art(): BelongsTo
    {
        return $this->belongsTo(Art::class, 'art_id');
    }

    public function candidates(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, SelectiveCandidate::class, 'selective_id', 'id', 'id', 'artist_id');
    }

    protected function startMoment(): Attribute
    {
        return $this->toDatetime();
    }

    protected function endMoment(): Attribute
    {
        return $this->toDatetime();
    }

    public function getActiveInterval(): array
    {
        return [
            'start_moment' => $this->getCarbon($this->start_moment, TimeStringFormat::DATE_TIME_FORMAT),
            'end_moment' => $this->getCarbon($this->end_moment, TimeStringFormat::DATE_TIME_FORMAT),
        ];
    }

    public function loadAllRelations(): Selective
    {
        return $this->load('art', 'enterprise', 'candidates');
    }

    public static function withAllRelations(): Builder
    {
        return static::with('art', 'enterprise', 'candidates');
    }

    public function save(array $options = []): bool
    {
        throw_unless(
            $this->enterprise->active,
            new CheckDBOperationException("The enterprise's account $this->enterprise_id is disabled")
        );

        return parent::save($options);
    }
}
