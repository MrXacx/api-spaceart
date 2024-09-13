<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Post",
 *     description="Schema of Model Post",
 *
 *     @OA\Property(property="id", type="int"),
 *     @OA\Property(property="user", ref="#/components/schemas/User"),
 *     @OA\Property(property="text", type="string", description="Text", example="Today i show me in Atlantic Resort"),
 *     @OA\Property(property="image", type="string", description="Image in base64 or url"),
 * )
 */
class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'text',
        'image',
    ];

    protected $hidden = [
        'user_id',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(get: fn (string $createdAt) => Carbon::parse($createdAt)->format('d/m/Y H:i'));
    }

    public static function withAllRelations(): Builder
    {
        return static::with('user');
    }

    public function loadAllRelations(): Post
    {
        return $this->load('user');
    }
}
