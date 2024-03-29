<?php

namespace App\Models;

use App\Models\Art;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Traits\HasDatetimeAccessorAndMutator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Selective extends Model
{
    use HasFactory, HasHiddenTimestamps, HasDatetimeAccessorAndMutator {
        HasHiddenTimestamps::__construct as hideTimestamps;
    }

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->hideTimestamps();
    }

    protected $fillable = [
        'title',
        'enterprise_id',
        'start_moment',
        'end_moment',
        'art_id',
        'note',
        'price',
    ];

    protected $hidden = [
        'enterprise_id',
        'art_id',
    ];
    
    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    public function art()
    {
        return $this->belongsTo(Art::class, 'art_id');
    }
    protected function startMoment(): Attribute
    {
        return $this->toDatetime();
    }
    protected function endMoment(): Attribute
    {
        return $this->toDatetime();
    }
}
