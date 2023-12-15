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
        'CNPJ',
        'company_name'
    ];

    protected function CNPJ(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Crypt::decrypt($value),
            set: fn (string $value) => Crypt::encryptString($value)   
        );
    }
}
