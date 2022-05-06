<?php
namespace App\Services\Music\Parts\Measures;

class MeasureKey
{
    protected static array $keys = [
         0 => [],
         1 => [],
         2 => [],
         3 => [],
         4 => [],
         5 => [],
         6 => [],
         7 => [],
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
        return $this->array[$index];
    }
}