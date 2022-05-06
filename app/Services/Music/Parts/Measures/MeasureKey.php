<?php
namespace App\Services\Music\Parts\Measures;

class MeasureKey
{
    protected static array $keys = [
         0 => [],
         1 => ['o5f'],
         2 => ['o5c', 'o5f'],
         3 => ['o5c', 'o5f', 'o5g'],
         4 => ['o5c', 'o5d', 'o5f', 'o5g'],
         5 => ['o4a', 'o5c', 'o5d', 'o5f', 'o5g'],
         6 => ['o4a', 'o5c', 'o5d', 'o5e', 'o5f', 'o5g'],
         7 => ['o4a', 'o4b', 'o5c', 'o5d', 'o5e', 'o5f', 'o5g'],
        -1 => [],
        -2 => [],
        -3 => [],
        -4 => [],
        -5 => [],
        -6 => [],
        -7 => [],
    ];

    public static function getCodes(int $index) : array
    {
        return static::$keys[$index] ?? [];
    }
}