<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Enumerate\Account;
use Enumerate\State;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable
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
        'CEP',
        'state',
        'city',
        'neighborhood',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'token'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    protected function state(): Attribute
    {
        return Attribute::make(fn (string $value) => State::tryFrom($value));
    }

    protected function type(): Attribute
    {
        return Attribute::make(
            get: fn (string $account) => Account::tryFrom($account),
        );
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn (string $password) => Crypt::encryptString($password),
        );
    }

    protected function token(): Attribute
    {
        return Attribute::make(
            get: fn (string $password) => Crypt::encryptString($password),
        );
    }
}
