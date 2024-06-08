<?php

namespace App\Models;

use App\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Annotations as OA;
use Thiagoprz\CompositeKey\HasCompositeKey;

/**
 * @OA\Schema(
 *     schema="Rate",
 *     description="Schema of Model Rate",
 *
 *     @OA\Property(property="author", ref="#/components/schemas/User"),
 *     @OA\Property(property="rated", ref="#/components/schemas/User"),
 *     @OA\Property(property="agreement", ref="#/components/schemas/Agreement"),
 *     @OA\Property(property="score", type="float", minimum=0, maximum=5, example="4.5"),
 *     @OA\Property(property="note", type="string", description="Short review", example="The artist is very helpful"),
 * )
 */
class Rate extends Model
{
    use HasCompositeKey, HasFactory, HasHiddenTimestamps {
        HasHiddenTimestamps::__construct as hideTimestamps;
    }

    protected $primaryKey = ['author_id', 'agreement_id'];

    public $incrementing = false;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->hideTimestamps();
    }

    protected $fillable = [
        'author_id',
        'rated_id',
        'agreement_id',
        'score',
        'note',
    ];

    protected $hidden = [
        'author_id',
        'rated_id',
        'agreement_id',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function rated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_id');
    }

    public function agreement(): BelongsTo
    {
        return $this->belongsTo(Agreement::class, 'agreement_id');
    }

    public function loadAllRelations(): Rate
    {
        return $this->load('author', 'rated', 'agreement');
    }

    public static function withAllRelations(): Builder
    {
        return static::with('author', 'rated', 'agreement');
    }
}
