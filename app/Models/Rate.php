<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'agreement_id',
        'score',
        'note',
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
