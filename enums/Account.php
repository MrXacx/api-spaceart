<?php

namespace Enumerate;

enum Account: string implements Enumerate
{
    case ARTIST = 'artist';
    case ENTERPRISE = 'enterprise';

    public static function parseCases(): array
    {
        return array_map(fn (Account $account) => $account->value, self::cases());
    }

    public static function get(string $n): Enumerate
    {
        return self::cases()[$n];
    }
}
