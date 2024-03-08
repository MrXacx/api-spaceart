<?php

namespace Enumerate;

use Enumerate\Traits\BackedEnumTrait;

enum AgreementStatus: string
{
    use BackedEnumTrait;

    case SEND = 'send';
    case ACCEPTED = 'accepted';
    case REFUSED = 'refused';
    case CENCELED = 'canceled';
}
