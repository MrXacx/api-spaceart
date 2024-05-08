<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasHiddenTimestamps;
use Enumerate\Account;
use Enumerate\State;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasHiddenTimestamps {
        HasHiddenTimestamps::__construct as hideTimestamps;
    }

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->hideTimestamps();
    }

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
        return Attribute::make(set: fn (string $value) => bcrypt($value));
    }

    protected function addressComplement(): Attribute
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

    public function sendRates(): HasMany
    {
        return $this->hasMany(Rate::class, 'author_id');
    }

    public function receivedRates(): HasMany
    {
        return $this->hasMany(Rate::class, 'rated_id');
    }

    public function artistAccountData(): BelongsTo
    {
        return $this->belongsTo(
            Artist::class,
            'id',
        );
    }

    public function enterpriseAccountData(): BelongsTo
    {
        return $this->belongsTo(
            Enterprise::class,
            'id',
        );
    }

    public function withAllRelations()
    {
        return $this->load('artistAccountData', 'enterpriseAccountData', 'sendRates', 'receivedRates');
    }
}
