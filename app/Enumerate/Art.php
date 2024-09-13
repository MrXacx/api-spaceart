<?php

namespace App\Enumerate;

use App\Traits\BackedEnumTrait;
use Doctrine\Common\Annotations\Annotation\Enum;

/**
 * @Enum(
 *     value={
 *         "acting",
 *         "dance",
 *         "music",
 *         "painting",
 *         "sculpture",
 *     }
 * )
 */
enum Art: string
{
    use BackedEnumTrait;

    case ACTING = 'acting';
    case DANCE = 'dance';
    case MUSIC = 'music';
    case PAINTING = 'painting';
    case SCULPTURE = 'sculpture';
}
