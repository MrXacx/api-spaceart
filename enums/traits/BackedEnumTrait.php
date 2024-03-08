<?php

namespace Enumerate\Traits;

trait BackedEnumTrait
{
    /**
     * @return array<int,int|string>
     */
    public static function values(): array
    {
        return array_map(fn($item) => $item->value, self::cases());
    }

    public static function get(string $n): self
    {
        return self::cases()[$n];
    }

    public static function value(string $n): string|int
    {
        return self::from($n)->value;
    }
}
