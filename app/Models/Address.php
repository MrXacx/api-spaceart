<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = null;
    protected $primaryKey = null;
    protected $fillable = [
        'postal_code',
        'state',
        'city',
        'neighborhood',
        'street',
        'address_complement',
    ];
}
