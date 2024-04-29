<?php

namespace App\Models;

use App\Models\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Thiagoprz\CompositeKey\HasCompositeKey;

class Rate extends Model
{
    use HasCompositeKey, HasFactory, HasHiddenTimestamps {
        HasHiddenTimestamps::__construct as hideTimestamps;
    }

    protected $primaryKey = ['author_id', 'agreement_id'];

    public $incrementing = false;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->hideTimestamps();
    }

    protected $fillable = [
        'author_id',
        'rated_id',
        'agreement_id',
        'score',
        'note',
    ];

    protected $hidden = [
        'author_id',
        'rated_id',
        'agreement_id',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function rated()
    {
        return $this->belongsTo(User::class, 'rated_id');
    }

    public function agreement()
    {
        return $this->belongsTo(Agreement::class, 'agreement_id');
    }
}
