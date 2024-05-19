<?php

namespace App\Models;

use App\Exceptions\NotSavedModelException;
use App\Models\Traits\HasDatetimeAccessorAndMutator;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory, HasDatetimeAccessorAndMutator;

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
        return $this->toDatetime();
    }

    public static function withAllRelations()
    {
        return static::with('user');
    }

    public function loadAllRelations()
    {
        return $this->load('user');
    }

    public function save($options = [])
    {
        throw_unless($this->user->active, NotSavedModelException::class);
        return parent::save($options);
    }

}
