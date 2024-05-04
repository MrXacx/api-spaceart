<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enterprise extends Model
{
    use HasFactory, HasHiddenTimestamps {
        HasHiddenTimestamps::__construct as hideTimestamps;
    }

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->hideTimestamps();
    }

    protected $fillable = [
        'id',
        'cnpj',
        'company_name',
    ];

    protected $hidden = [
        'cnpj',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function agreements()
    {
        return $this->hasMany(Agreement::class, 'enterprise_id');
    }

    public function selectives()
    {
        return $this->hasMany(Selective::class, 'enterprise_id');
    }

    protected function cnpj(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => Crypt::decryptString($value),
            set: fn(string $value) => Crypt::encryptString($value)
        );
    }

    public function withAllRelations()
    {
        return $this->load('user', 'agreements', 'selectives');
    }

    public function toArray()
    {
        $this->load('user');

        $enterpriseArray = parent::toArray();
        $userArray = $enterpriseArray['user'];
        unset($enterpriseArray['user']);

        return  $enterpriseArray + $userArray;
    }
}
