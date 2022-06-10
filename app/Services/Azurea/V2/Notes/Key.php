<?php
namespace App\Services\Azurea\V2\Notes;

class Key
{
    protected string $pitchStep;

    protected int $pitchOctave;

    protected int $key;

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
       -2 => ['b', 'e'],
       -3 => ['a', 'b', 'e'],
       -4 => ['a', 'b', 'd', 'e'],
       -5 => ['g', 'a', 'b', 'd', 'e'],
       -6 => ['g', 'a', 'b', 'c', 'd', 'e'],
       -7 => ['f', 'g', 'a', 'b', 'c', 'd', 'e'],
    ];

    public function setKey(int $key) : void
    {
        $this->key = $key;
    }
    
    public function setPitchStep(string $pitchStep) : void
    {
        $this->pitchStep = $pitchStep;
    }
    
    public function setPitchOctave(int $pitchOctave) : void
    {
        $this->pitchOctave = $pitchOctave;
    }

    public function newPitch() : string
    {
        return $this->pitchStep;
    }

    public function newOctave() : int
    {
        return $this->pitchOctave;
    }

}