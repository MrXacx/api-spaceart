<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Enumerate\Account;
use Enumerate\State;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class User extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'image',
        'postal_code',
        'state',
        'city',
        'neighborhood',
        'address',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        //'token',
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
        return Attribute::make(
            set: fn (string $password) => Crypt::encryptString($password),
        );
    }

    protected function token(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Crypt::encryptString($value),
        );
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Crypt::decryptString($value),
            set: fn (string $value) => Crypt::encryptString($value),
        );
    }

    protected function address(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? Crypt::decryptString($value) : null,
            set: fn (string $value) => Crypt::encryptString($value),
        );
    }

    protected function neighborhood(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => ! is_null($value) ? Crypt::decryptString($value) : null,
            set: fn (string $value) => Crypt::encryptString($value),
        );
    }

    protected function state(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value instanceof State ? $value : State::tryFrom($value)
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Crypt::decryptString($value),
            set: fn (string $value) => Crypt::encryptString($value),
        );
    }

    protected function postalCode(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Crypt::decryptString($value),
            set: fn (string $value) => Crypt::encryptString($value),
        );
    }

    protected function type(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value instanceof Account ? $value : Account::tryFrom($value)
        );
    }
}
