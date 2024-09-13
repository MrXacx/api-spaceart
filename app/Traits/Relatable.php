<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

trait Relatable
{
    private Model $thi;

    protected static array $countables = [];

    protected static array $avg = [];

    protected static array $relatables = [];

    public function loadAllRelations(): self
    {
        $this->loadMissing($this->relatables)->loadCount($this->countables);

        foreach ($this->avg as $name => $column) {
            $this->loadAvg($name, $column);
        }

        return $this;
    }

    public static function withAllRelations(): Builder
    {
        foreach (static::$avg as $name => $column) {
            static::withAvg($name, $column);
        }

        return static::with(static::$relatables)::withCount(static::$countables);
    }
}
