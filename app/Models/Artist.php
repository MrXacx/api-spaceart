<?php

namespace App\Models;

use App\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Crypt;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Artist",
 *     description="Schema of Model Artist User",
 *
 *     @OA\Property(property="cpf", type="string", example="40033796599"),
 *     @OA\Property(property="bithday", type="date", example="01/01/1970"),
 *     @OA\Property(property="wage", type="number", example="100"),
 *     @OA\Property(property="art", ref="#/components/schemas/Art"),
 * )
 */
class Artist extends Model
{
    use HasFactory, HasHiddenTimestamps {
        HasHiddenTimestamps::__construct as hideTimestamps;
    }

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->hideTimestamps();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'cpf',
        'art_id',
        'birthday',
        'wage',
    ];

    protected $hidden = [
        'id',
        'cpf',
        'art_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date:d/m/Y',
    ];

    public function art(): BelongsTo
    {
        return $this->belongsTo(Art::class);
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class);
    }

    public function candidatures(): HasManyThrough
    {
        return $this->hasManyThrough(Selective::class, SelectiveCandidate::class, 'artist_id', 'id', 'id', 'artist_id');
    }

    protected function cpf(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Crypt::decryptString($value),
            set: fn (string $value) => Crypt::encryptString($value)
        );
    }

    public function loadAllRelations(): Artist
    {
        return $this->load('art', 'user', 'agreements', 'candidatures');
    }

    public static function withAllRelations(): Builder
    {
        return static::with('art', 'user', 'agreements', 'candidatures');
    }

    public function showConfidentialData(): Artist
    {
        return $this->makeVisible('cpf');
    }

    public function toArray(): array
    {
        $this->loadMissing('art', 'agreements', 'candidatures');
        return parent::toArray();
    }
}
