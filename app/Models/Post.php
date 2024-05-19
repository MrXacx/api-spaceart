<?php

namespace App\Models;

use App\Exceptions\NotSavedModelException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function save($options = []): bool
    {
        throw_unless($this->user->active, NotSavedModelException::class);

        return parent::save($options);
    }
}
