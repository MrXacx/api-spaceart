<?php

namespace App\Models;

use Enumerate\Art;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Selective extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'owner', 'start_moment', 'end_moment', 'art', 'description', 'price',
    ];
    
    protected function art(){
        return Attribute::make(
            get: fn(string $art) => Art::tryFrom($art),
        );
    }
}