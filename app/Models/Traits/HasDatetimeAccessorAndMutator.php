<?php

namespace App\Models\Traits;

use Closure;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Centraliza métodos de formatação de datas e horários para Models 
 */
trait HasDatetimeAccessorAndMutator
{
  private function getDatetimeAccessorAndMutator(string $accessorFormat, string $mutatorFormat)
  {
    return Attribute::make(
      get: fn(string $datetime) => Carbon::parse($datetime)->format($accessorFormat),
      set: fn(string $datetime) => Carbon::parse($datetime)->format($mutatorFormat),
    );
  }

  private function toDatetime(): Attribute
  {
    return $this->getDatetimeAccessorAndMutator('d/m/Y H:i', 'Y-m-d H:i:s');
  }
  private function toDate(): Attribute
  {
    return $this->getDatetimeAccessorAndMutator('d/m/Y', 'Y-m-d');
  }
  private function toTime(): Attribute
  {
    return $this->getDatetimeAccessorAndMutator('H:i', 'H:i:s');
  }
}
