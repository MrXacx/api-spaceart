<?php

namespace App\Models;

use App\Models\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    use HasFactory, HasHiddenTimestamps;

    protected $fillable = [
        'enterprise_id',
        'artist_id',
        'note',
        'date',
        'start_time',
        'end_time',
        'price',
        'art_id',
        'status',
    ];

    protected $hidden = [
        'enterprise_id',
        'artist_id',
        'art_id',
    ];

    protected $cast = [
        'date' => 'date:d/m/Y',
        'start_time' => 'time:H:i',
        'end_time' => 'time:H:i',
    ];

    protected function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    protected function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }

    protected function art()
    {
        return $this->belongsTo(Art::class, 'art_id');
    }
}
