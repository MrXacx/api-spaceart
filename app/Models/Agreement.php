<?php

namespace App\Models;

use App\Exceptions\CheckDBOperationException;
use App\Models\Traits\HasDatetimeAccessorAndMutator;
use App\Models\Traits\HasHiddenTimestamps;
use Enumerate\TimeStringFormat;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Agreement",
 *     description="Schema of Model Agreement",
 *
 *     @OA\Property(property="id", type="int"),
 *     @OA\Property(property="enterprise", ref="#/components/schemas/User"),
 *     @OA\Property(property="artist", ref="#/components/schemas/User"),
 *     @OA\Property(property="art", ref="#/components/schemas/Art"),
 *     @OA\Property(property="note", type="string",  description="Description of service", example="Rock musical show for 2 hours"),
 *     @OA\Property(property="date", type="date", description="Day of service", example="01/01/2025"),
 *     @OA\Property(property="start_time", type="date", description="Hour of service", example="18:30"),
 *     @OA\Property(property="end_time", type="date", description="Hour of finish service", example="20:30"),
 * )
 */
class Agreement extends Model
{
    use HasDatetimeAccessorAndMutator, HasFactory, HasHiddenTimestamps {
        HasHiddenTimestamps::__construct as private hideTimestamps;
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->hideTimestamps();
    }

    protected $fillable = [
        'enterprise_id',
        'artist_id',
        'note',
        'date',
        'start_time',
        'end_time',
        'price',
        'art_id',
        'status',
    ];

    protected $hidden = [
        'enterprise_id',
        'artist_id',
        'art_id',
        'laravel_through_key',
    ];

    public function enterprise(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Enterprise::class, 'id', 'id', 'enterprise_id');
    }

    public function artist(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Artist::class, 'id', 'id', 'artist_id');
    }

    public function art(): BelongsTo
    {
        return $this->belongsTo(Art::class, 'art_id');
    }

    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class, 'agreement_id');
    }

    protected function date(): Attribute
    {
        return $this->toDate();
    }

    protected function startTime(): Attribute
    {
        return $this->toTime();
    }

    protected function endTime(): Attribute
    {
        return $this->toTime();
    }

    public function getActiveInterval(): array
    {
        return [
            'start_moment' => $this->getCarbon("$this->date $this->start_time", TimeStringFormat::DATE_TIME_FORMAT),
            'end_moment' => $this->getCarbon("$this->date $this->end_time", TimeStringFormat::DATE_TIME_FORMAT),
        ];
    }

    public function loadAllRelations()
    {
        return $this->load('art', 'artist', 'enterprise', 'rates');
    }

    public static function withAllRelations()
    {
        return static::with('art', 'artist', 'enterprise', 'rates');
    }

    /**
     * @throws CheckDBOperationException
     */
    public function save(array $options = []): bool
    {
        throw_unless($this->artist->active, new CheckDBOperationException("The artist's account $this->artist_id is disabled"));
        throw_unless($this->enterprise->active, new CheckDBOperationException("The enterprise's account $this->enterprise_id is disabled"));

        return parent::save($options);
    }
}
