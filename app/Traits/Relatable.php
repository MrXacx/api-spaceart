<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

trait Relationable
{
    private Model $thi;
    protected $countable = [];
    protected $relations = [];
    
    public function loadAll(){
        
    }
}
