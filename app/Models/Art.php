<?php

namespace App\Models;

use App\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Art",
 *     description="Schema of Model Art",
 *
 *     @OA\Property(property="name", type="enum", enum="App\Enumerate\Art"),
 *     @OA\Property(property="artists_count", type="int", description="number of relationed artists"),
 *     @OA\Property(property="agreements_count", type="int", description="number of relationed agreements"),
 *     @OA\Property(property="selectives_count", type="int", description="number of relationed selectives"),
 * )
 */
class Art extends Model
{
    use HasFactory, HasHiddenTimestamps;

    protected $table = 'arts';

    protected $hidden = ['id'];

    protected $casts = ['name' => \App\Enumerate\Art::class];

    public function artists(): HasMany
    {
        return $this->hasMany(Artist::class);
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class);
    }

    public function selectives(): HasMany
    {
        return $this->hasMany(Selective::class);
    }
}
