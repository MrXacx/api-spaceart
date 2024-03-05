<?php

namespace App\Models;

use Enumerate\Art;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Selective extends Model
{
    use HasFactory;

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
        'start_moment' => 'datetime',
        'end_moment' => 'datetime',
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
