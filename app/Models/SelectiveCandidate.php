<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectiveCandidate extends Model
{
    use HasFactory;

    protected $fillable = ['artist', 'selective'];
}
