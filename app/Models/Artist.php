<?php

namespace App\Models;

use App\Models\Art;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'art_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date:d/m/Y',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function art()
    {
        return $this->belongsTo(Art::class);
    }

    public function agreements()
    {
        return $this->hasMany(Agreement::class);
    }
    public function candidatures()
    {
        return $this->hasManyThrough(Selective::class, SelectiveCandidate::class, 'artist_id', 'id', 'id', 'artist_id');
    }

    protected function cpf(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => Crypt::decryptString($value),
            set: fn(string $value) => Crypt::encryptString($value)
        );
    }

    public function withAllRelations()
    {
        return $this->load('art', 'user', 'agreements', 'candidatures');
    }

    public function toArray()
    {
        $this->load('user', 'art');
        
        $artistArray = parent::toArray();
        $userArray = $artistArray['user'];
        unset($artistArray['user']);
        
        return  $artistArray + $userArray;
    }
}
