<?php

namespace App\Models;

use App\Models\Traits\HasHiddenTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory, HasHiddenTimestamps {
        HasHiddenTimestamps::__construct as hideTimestamps;
    }

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->hideTimestamps();
    }

    protected $fillable = [
        'user_id',
        'agreement_id',
        'score',
        'note',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function agreement()
    {
        return $this->belongsTo(Agreement::class, 'agreement_id');
    }
}
