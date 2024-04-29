<?php

namespace App\Models;

use App\Models\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

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

    protected function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    protected function cnpj(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Crypt::decryptString($value),
            set: fn (string $value) => Crypt::encryptString($value)
        );
    }

    public function agreements()
    {
        return $this->hasMany(Agreement::class, 'enterprise_id');
    }
}
