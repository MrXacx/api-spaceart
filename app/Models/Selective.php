<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Selective extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'owner', 'start_moment', 'end_moment', 'art', 'description', 'price',
    ];
}
