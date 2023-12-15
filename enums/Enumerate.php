<?php

namespace Enumerate;

interface Enumerate
{
    /**
     * @return array<int, string>
     */
    public static function parseCases(): array;

    public static function get(string $n): Enumerate;
}
