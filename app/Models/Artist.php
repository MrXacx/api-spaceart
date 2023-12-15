<?php

namespace App\Models;

use Enumerate\Art;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class Artist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'CPF',
        'art',
        'birthday'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date'
    ];

    protected function CPF(): Attribute
    {
        return Attribute::make(fn(string $value) => Crypt::encrypt($value));
    }
    protected function art(): Attribute
    {
        return Attribute::make(fn(string $value) => Art::tryFrom($value));
    }

}
