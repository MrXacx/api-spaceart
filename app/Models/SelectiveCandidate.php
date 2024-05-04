<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasHiddenTimestamps;
use Thiagoprz\CompositeKey\HasCompositeKey;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SelectiveCandidate extends Model
{
    use HasFactory, HasHiddenTimestamps, HasCompositeKey {
        HasHiddenTimestamps::__construct as hideTimestamps;
    }

    protected $primary = ['artist_id', 'selective_id'];

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->hideTimestamps();
    }

    protected $fillable = ['artist_id', 'selective_id'];
    protected $hidden = ['artist_id', 'selective_id'];

    protected function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }

    protected function selective()
    {
        return $this->belongsTo(Selective::class, 'selective_id');
    }

    public function withAllRelations()
    {
        return $this->load('artist', 'selective');
    }
}
