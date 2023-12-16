<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    use HasFactory;

    protected $fillable = [
        "hirer",
        "hired",
        "description",
        "date",
        "start_time",
        "end_time",
        "price",
        "art",
        "status",
    ];
}
