<?php

declare(strict_types=1);

namespace App\Models;

use Enumerate\State;
use Enumerate\Account;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Traits\HasHiddenTimestamps;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasHiddenTimestamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'active',
        'email',
        'phone',
        'password',
        'type',
        'postal_code',
        'state',
        'city',
        'neighborhood',
        'street',
        'address_complement',
        'image',
        'slug',
        'biography',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'phone',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'type' => Account::class,
        'state' => State::class,
    ];


    protected function password(): Attribute
    {
        return Attribute::make(set: fn(string $value) => bcrypt($value));
    }

    protected function addressComplement(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? Crypt::decryptString($value) : null,
            set: fn(string $value) => Crypt::encryptString($value),
        );
    }

    protected function neighborhood(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => !is_null($value) ? Crypt::decryptString($value) : null,
            set: fn(string $value) => Crypt::encryptString($value),
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => Crypt::decryptString($value),
            set: fn(string $value) => Crypt::encryptString($value),
        );
    }

    protected function postalCode(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => Crypt::decryptString($value),
            set: fn(string $value) => Crypt::encryptString($value),
        );
    }
}
