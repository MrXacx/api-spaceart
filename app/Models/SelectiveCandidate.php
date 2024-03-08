<?php

namespace App\Models;

use App\Models\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectiveCandidate extends Model
{
    use HasFactory, HasHiddenTimestamps;

    protected $fillable = ['artist_id', 'selective_id'];

    protected function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }

    protected function selective()
    {
        return $this->belongsTo(Selective::class, 'selective_id');
    }
}
