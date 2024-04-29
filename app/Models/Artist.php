<?php

namespace App\Models;

use App\Models\Traits\HasHiddenTimestamps;
use Enumerate\Art;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Artist extends Model
{
    use HasFactory, HasHiddenTimestamps {
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
        'id',
        'cpf',
        'art_id',
        'birthday',
        'wage',
    ];

    protected $hidden = [
        'cpf',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date:d/m/Y',
    ];

    public function agreements()
    {
        return $this->hasMany(Agreement::class);
    }

    protected function cpf(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Crypt::decryptString($value),
            set: fn (string $value) => Crypt::encryptString($value)
        );
    }

    protected function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    protected function art()
    {
        return $this->belongsTo(Art::class, 'art_id');
    }
}
