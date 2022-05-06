<?php
namespace App\Services\Music\Parts\Measures;

use Illuminate\Support\Collection;

class MeasureKey
{
    protected int $index;

    protected array $keys = [
         0 => [],
         1 => ['f'],
         2 => ['c', 'f'],
         3 => ['c', 'f', 'g'],
         4 => ['c', 'd', 'f', 'g'],
         5 => ['a', 'c', 'd', 'f', 'g'],
         6 => ['a', 'c', 'd', 'e', 'f', 'g'],
         7 => ['a', 'b', 'c', 'd', 'e', 'f', 'g'],
        -1 => ['b'],
        -2 => ['b', 'd'],
        -3 => ['a', 'b', 'd'],
        -4 => ['a', 'b', 'd', 'e'],
        -5 => ['g', 'a', 'b', 'd', 'e'],
        -6 => ['g', 'a', 'b', 'c', 'd', 'e'],
        -7 => ['f', 'g', 'a', 'b', 'c', 'd', 'e'],
    ];

    public function __construct(int $index)
    {
        $this->index = $index;
    }

    public static function factory(int $index)
    {
        return new self($index);
    }

    protected function keys() : Collection
    {
        $keys = collect($this->keys);
        $list = $keys->get($this->index);
        return collect($list);
    }
    
    public function isSharp(string $pitch) : bool
    {
        if ($this->index > 0) {
            return $this->keys()->contains($pitch);
        }
        return false;
    }

    public function isFlat(string $pitch) : bool
    {
        if ($this->index < 0) {
            return $this->keys()->contains($pitch);
        }
        return false;
    }
}