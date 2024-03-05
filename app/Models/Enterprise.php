<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enterprise extends Model
{
    use HasFactory;

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
}
