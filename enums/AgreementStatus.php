<?php

namespace Enumerate;

use Enumerate\Extension\BackedEnumTrait;

enum AgreementStatus: string
{
    use BackedEnumTrait;

    case SEND = 'send';
    case ACCEPTED = 'accepted';
    case RECUSED = 'recused';
    case CENCELED = 'canceled';
}
