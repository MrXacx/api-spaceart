<?php

namespace App\Enumerate;

use App\Traits\BackedEnumTrait;

enum Account: string
{
    use BackedEnumTrait;

    case ARTIST = 'artist';
    case ENTERPRISE = 'enterprise';
}
