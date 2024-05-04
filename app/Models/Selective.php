<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Models\Traits\HasDatetimeAccessorAndMutator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Selective extends Model
{
    use HasDatetimeAccessorAndMutator, HasFactory, HasHiddenTimestamps {
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

    public function candidates()
    {
        return $this->hasManyThrough(Artist::class, SelectiveCandidate::class, 'selective_id', 'id', 'id', 'artist_id');
    }

    protected function startMoment(): Attribute
    {
        return $this->toDatetime();
    }

    protected function endMoment(): Attribute
    {
        return $this->toDatetime();
    }

    public function withAllRelations()
    {
        return $this->load('art', 'enterprise', 'candidates');
    }
}
