<?php

namespace Enumerate;

enum State: string implements Enumerate
{
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

    public static function parseCases(): array
    {
        return array_map(fn (State $state) => $state->value, self::cases());
    }

    public static function get(string $n): Enumerate
    {
        return self::cases()[$n];
    }
}
