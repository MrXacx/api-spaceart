<?php

namespace App\Enumerate;

use App\Traits\BackedEnumTrait;
use Doctrine\Common\Annotations\Annotation\Enum;

/**
 * @Enum(
 *     value={
 *         "send",
 *         "accepted",
 *         "refused",
 *         "canceled",
 *     }
 * )
 */
enum AgreementStatus: string
{
    use BackedEnumTrait;

    case SEND = 'send';
    case ACCEPTED = 'accepted';
    case REFUSED = 'refused';
    case CANCELED = 'canceled';
}
