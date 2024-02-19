<?php

namespace App\Models;

use Enumerate\Art;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class Artist extends User
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cpf',
        'art',
        'birthday',
        'wage',
    ];

    protected $hidden = ['cpf'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date',
        'art' => Art::class,
    ];

    protected function cpf(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Crypt::decryptString($value),
            set: fn (string $value) => Crypt::encryptString($value)
        );
    }

    protected function art(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value instanceof Art ? $value : Art::tryFrom($value)
        );
    }
}
