<?php

namespace Enumerate;

use Enumerate\Traits\BackedEnumTrait;

enum Account: string
{
    use BackedEnumTrait;

    case ARTIST = 'artist';
    case ENTERPRISE = 'enterprise';
}
