<?php

namespace App\Models;

use App\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Enterprise",
 *     description="Schema of Model Enterprise User",
 *
 *     @OA\Property(property="cnpj", type="string", example="01499146000196"),
 *     @OA\Property(property="company_name", type="string", example="José e Gabriela Esportes ME"),
 *     @OA\Property(property="agreements", type="array", @OA\Items(ref="#/components/schemas/Agreement")),
 *     @OA\Property(property="selectives", type="array", @OA\Items(ref="#/components/schemas/Selective"))
 * )
 */
class Enterprise extends Model
{
    use HasFactory, HasHiddenTimestamps {
        HasHiddenTimestamps::__construct as hideTimestamps;
    }

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->hideTimestamps();
    }

    protected $fillable = [
        'id',
        'cnpj',
        'company_name',
    ];

    protected $hidden = [
        'id',
        'cnpj',
    ];

    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class, 'enterprise_id');
    }

    public function selectives(): HasMany
    {
        return $this->hasMany(Selective::class, 'enterprise_id');
    }

    protected function cnpj(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Crypt::decryptString($value),
            set: fn (string $value) => Crypt::encryptString($value)
        );
    }

    public function loadAllRelations(): Enterprise
    {
        return $this->load('agreements', 'selectives');
    }

    public static function withAllRelations(): Builder
    {
        return static::with('agreements', 'selectives');
    }

    public function showConfidentialData(): Enterprise
    {
        return $this->makeVisible('cnpj');
    }
}
