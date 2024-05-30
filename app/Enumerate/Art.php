<?php

namespace App\Enumerate;

use App\Traits\BackedEnumTrait;

enum Art: string
{
    use BackedEnumTrait;

    case ACTING = 'acting';
    case DANCE = 'dance';
    case MUSIC = 'music';
    case PAINTING = 'painting';
    case SCULPTURE = 'sculpture';
}
