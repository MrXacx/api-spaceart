<?php

namespace App\Enumerate;

use App\Traits\BackedEnumTrait;
use Doctrine\Common\Annotations\Annotation\Enum;

/**
 * @Enum(
 *     value={"artist", "enterprise"},
 * )
 */
enum Account: string
{
    use BackedEnumTrait;

    case ARTIST = 'artist';
    case ENTERPRISE = 'enterprise';
}
