<?php

namespace App\Models;

use App\Models\Traits\HasHiddenTimestamps;
use Enumerate\Art;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Selective extends Model
{
    use HasFactory, HasHiddenTimestamps;

    protected $fillable = [
        'title',
        'enterprise_id',
        'start_moment',
        'end_moment',
        'art_id',
        'note',
        'price',
    ];

    protected $cast = [
        'start_moment' => 'datetime:d/m/Y H:i:s',
        'end_moment' => 'datetime:d/m/Y H:i:s',
    ];

    protected function enterprise()
    {
        return $this->belongsTo(Art::class, 'enterprise_id');
    }

    protected function art()
    {
        return $this->belongsTo(Art::class, 'art_id');
    }
}
