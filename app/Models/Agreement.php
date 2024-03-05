<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    use HasFactory;

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

    protected $cast = [
        'date' => 'date',
        'start_time' => 'time',
        'end_time' => 'time',
    ];

    protected function enterprise()
    {
        return $this->belongsTo(Art::class, 'enterprise_id');
    }
    protected function artist()
    {
        return $this->belongsTo(Art::class, 'artist_id');
    }
    protected function art()
    {
        return $this->belongsTo(Art::class, 'art_id');
    }
}
