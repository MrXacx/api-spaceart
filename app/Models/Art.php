<?php

namespace App\Models;

use App\Models\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Art extends Model
{
    use HasFactory, HasHiddenTimestamps;

    protected $table = 'arts';

    protected $hidden = ['id'];

    public function artists(): HasMany
    {
        return $this->hasMany(Artist::class);
    }
    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class);
    }
    public function selectives(): HasMany
    {
        return $this->hasMany(Selective::class);
    }

    public function toArray()
    {
        $this->loadCount('artists', 'agreements', 'selectives');
        return parent::toArray();
    }
}
