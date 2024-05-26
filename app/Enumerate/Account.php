<?php

namespace App\Enumerate;

use App\Trait\BackedEnumTrait;

enum Account: string
{
    use BackedEnumTrait;

    case ARTIST = 'artist';
    case ENTERPRISE = 'enterprise';
}
