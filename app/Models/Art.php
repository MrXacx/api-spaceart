<?php

namespace App\Models;

use App\Models\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Art extends Model
{
    use HasFactory, HasHiddenTimestamps;

    protected $table = 'arts';
}
