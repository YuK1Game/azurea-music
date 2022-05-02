<?php
namespace App\Services\Azurea\Notes;

use Illuminate\Support\Collection;

class NotePitchTable
{
    protected static array $pitchTable = ['c', 'c+', 'd', 'd+', 'e', 'f', 'f+', 'g', 'g+', 'a', 'a+', 'b'];

    public static function pitchTable() : Collection
    {
        return collect(static::$pitchTable);
    }

    public static function addPitch(string $pitchStep, int $pitchOctave) : array
    {
        $pitchTableIndex = static::pitchTable()->search(strtolower($pitchStep));
        $pitchTableIndex++;

        if ($newPitchStep = static::pitchTable()->get($pitchTableIndex)) {
            return ['c', $pitchOctave + 1];
        }
        return [ $newPitchStep, $pitchOctave ];
    }

    public static function subPitch(string $pitchStep, int $pitchOctave) : array
    {
        $pitchTableIndex = static::pitchTable()->search(strtolower($pitchStep));
        $pitchTableIndex--;

        if ($newPitchStep = static::pitchTable()->get($pitchTableIndex)) {
            return ['b', $pitchOctave - 1];
        }
        return [ $newPitchStep, $pitchOctave ];
    }
}