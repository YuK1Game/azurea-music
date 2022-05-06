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

    public static function addPitch(string $pitchStep, int $pitchOctave, int $addCount = 1) : array
    {
        for ($i = 0 ; $i < $addCount ; $i++) {
            $pitchTableIndex = static::pitchTable()->search(strtolower($pitchStep));
            $pitchTableIndex++;

            if ( ! $pitchStep = static::pitchTable()->get($pitchTableIndex)) {
                $pitchStep    = 'c';
                $pitchOctave += 1;
            }
        }

        return [ $pitchStep, $pitchOctave ];
    }

    public static function subPitch(string $pitchStep, int $pitchOctave, int $subCount = 1) : array
    {
        for ($i = 0 ; $i < $subCount ; $i++) {
            $pitchTableIndex = static::pitchTable()->search(strtolower($pitchStep));
            $pitchTableIndex--;

            if ( ! $pitchStep = static::pitchTable()->get($pitchTableIndex)) {
                $pitchStep    = 'b';
                $pitchOctave -= 1;
            }
        }

        return [ $pitchStep, $pitchOctave ];
    }
}