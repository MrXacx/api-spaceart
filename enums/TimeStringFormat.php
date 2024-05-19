<?php

namespace Enumerate;

use Enumerate\Traits\BackedEnumTrait;

enum TimeStringFormat: string
{
    use BackedEnumTrait;
    case DATE_TIME_FORMAT = 'd/m/Y H:i';
    case INTERNAL_DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    case TIME_FORMAT = 'H:i';
    case INTERNAL_TIME_FORMAT = 'H:i:s';
    case DATE_FORMAT = 'd/m/Y';
    case INTERNAL_DATE_FORMAT = 'Y-m-d';
}
