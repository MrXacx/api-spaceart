<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Models\Traits\HasDatetimeAccessorAndMutator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agreement extends Model
{
    use HasDatetimeAccessorAndMutator, HasFactory, HasHiddenTimestamps {
        HasHiddenTimestamps::__construct as private hideTimestamps;
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->hideTimestamps();
    }

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

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }

    public function art()
    {
        return $this->belongsTo(Art::class, 'art_id');
    }

    public function rates()
    {
        return $this->hasMany(Rate::class, 'agreement_id');
    }

    protected function date(): Attribute
    {
        return $this->toDate();
    }

    protected function startTime(): Attribute
    {
        return $this->toTime();
    }

    protected function endTime(): Attribute
    {
        return $this->toTime();
    }

    public function withAllRelations()
    {
        return $this->load('art', 'artist', 'enterprise', 'rates');
    }
}
