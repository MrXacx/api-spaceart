<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Enumerate\TimeStringFormat;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Centraliza métodos de formatação de datas e horários para Models
 */
trait HasDatetimeAccessorAndMutator
{
    /**
     * @return Attribute
     */
    private function getDatetimeAccessorAndMutator(TimeStringFormat $accessorFormat, TimeStringFormat $mutatorFormat)
    {
        $mutatorFormat = $mutatorFormat->value;
        $accessorFormat = $accessorFormat->value;

        return Attribute::make(
            get: fn (string $datetime) => Carbon::parse($datetime)->format($accessorFormat),
            set: fn (string $datetime) => Carbon::createFromFormat($accessorFormat, $datetime)->format($mutatorFormat),
        );
    }

    private function toDatetime(): Attribute
    {
        return $this->getDatetimeAccessorAndMutator(TimeStringFormat::DATE_TIME_FORMAT, TimeStringFormat::INTERNAL_DATE_TIME_FORMAT);
    }

    private function toDate(): Attribute
    {
        return $this->getDatetimeAccessorAndMutator(TimeStringFormat::DATE_FORMAT, TimeStringFormat::INTERNAL_DATE_FORMAT);
    }

    private function toTime(): Attribute
    {
        return $this->getDatetimeAccessorAndMutator(TimeStringFormat::TIME_FORMAT, TimeStringFormat::INTERNAL_TIME_FORMAT);
    }

    private function getCarbon(string $timeString, TimeStringFormat $format): Carbon
    {
        return Carbon::createFromFormat($format->value, $timeString);
    }
}
