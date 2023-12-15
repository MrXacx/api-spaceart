<?php

namespace Enumerate;

use Enumerate\Extension\BackedEnumTrait;

enum State: string
{
    use BackedEnumTrait;

    case AM = 'AM';
    case BA = 'BA';
    case CE = 'CE';
    case AL = 'AL';
    case AC = 'AC';
    case TO = 'TO';
    case RR = 'RR';
    case RO = 'RO';
    case AP = 'AP';
    case PA = 'PA';
    case MA = 'MA';
    case RN = 'RN';
    case PI = 'PI';
    case PE = 'PE';
    case SE = 'SE';
    case MT = 'MT';
    case MS = 'MS';
    case GO = 'GO';
    case SP = 'SP';
    case MG = 'MG';
    case ES = 'ES';
    case RJ = 'RJ';
    case RS = 'RS';
    case SC = 'SC';
    case PR = 'PR';
}
