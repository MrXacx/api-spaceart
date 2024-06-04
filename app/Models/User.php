<?php

declare(strict_types=1);

namespace App\Models;

use App\Enumerate\Account;
use App\Enumerate\State;
use App\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="User",
 *     description="Schema of Model User",
 *
 *     @OA\Property( property="id", type="int", example="1" ),
 *     @OA\Property( property="name", type="string", description="Username", example="José Carlos" ),
 *     @OA\Property( property="active", type="bool", description="Access to account is enabled", default="true" ),
 *     @OA\Property( property="email", type="string", description="Access email address", example="example@org.net" ),
 *     @OA\Property( property="phone", type="string", description="User's mobile number", example="71988469787"),
 *     @OA\Property( property="password", type="string",  example="<FO<k2&K83.;<RAeiC?@"),
 *     @OA\Property( property="type",type="enum", enum="App\Enumerate\Account", description="Account type"),
 *     @OA\Property( property="postal_code", type="string", description="Brazilian zip code", example="41000000"),
 *     @OA\Property( property="state", type="string", description="Brazilian state acronym", example="BA"),
 *     @OA\Property( property="city", type="string", example="Salvador"),
 *     @OA\Property( property="neighborhood", type="string", example="Piatã"),
 *     @OA\Property( property="street", type="string", example="Av. Orlando Gomes"),
 *     @OA\Property( property="address_complement", type="string", description="Additional  information of address", example="Beside of SESI Saúde"),
 *     @OA\Property( property="image", type="string", description="Image in base64 or URL"),
 *     @OA\Property( property="slug", type="string", description="User's website address", example="https://spaceart-lemon.vercel.app/user/2"),
 *     @OA\Property( property="biography", type="string", description="Short presentation text"),
 *     @OA\Property( property="received_rates_avg_score", type="float"),
 *     @OA\Property( property="artist_account_data", ref="#/components/schemas/Artist"),
 *     @OA\Property( property="enterprise_account_data", ref="#/components/schemas/Enterprise"),
 *     @OA\Property( property="sent_rates", type="array", @OA\Items(ref="#/components/schemas/Rate")),
 *     @OA\Property( property="received_rates", type="array", @OA\Items(ref="#/components/schemas/Rate")),
 *     @OA\Property( property="posts", type="array", @OA\Items(ref="#/components/schemas/Post")),
 * )
 */
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
        'email',
        'password',
        'phone',
        'laravel_through_key',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'bool',
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

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function enterpriseAccountData(): BelongsTo
    {
        return $this->belongsTo(
            Enterprise::class,
            'id',
        );
    }

    public function loadAllRelations(): User
    {
        return $this
            ->load('artistAccountData', 'enterpriseAccountData', 'sendRates', 'receivedRates', 'posts')
            ->loadAvg('receivedRates', 'score');
    }

    public static function withAllRelations(): Builder
    {
        return static::with('artistAccountData', 'enterpriseAccountData', 'sendRates', 'receivedRates', 'posts')
            ->withAvg('receivedRates', 'score');
    }

    public function showConfidentialData(): User
    {
        $this->makeVisible('phone', 'email');
        $this->artistAccountData?->showConfidentialData();
        $this->enterpriseAccountData?->showConfidentialData();

        return $this;
    }
}
