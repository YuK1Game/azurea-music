<?php
namespace App\Services\Azurea\V2\Notes;

use Illuminate\Support\Collection;

class Key
{
    protected string $pitchStep;

    protected int $pitchOctave;

    protected ?int $pitchAlter = null;

    protected int $sharpCount = 0;

    protected int $flatCount = 0;

    protected int $key = 0;

    protected array $list = [
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

    protected array $pitchStepTable = ['c', 'c+', 'd', 'd+', 'e', 'f', 'f+', 'g', 'g+', 'a', 'a+', 'b'];


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

    public function setPitchAlter(?int $pitchAlter) : void
    {
        $this->pitchAlter = $pitchAlter;
    }

    public function setSharpCount(int $sharpCount) : void
    {
        $this->sharpCount = $sharpCount;
    }

    public function setFlatCount(int $flatCount) : void
    {
        $this->flatCount = $flatCount;
    }

    public function newPitch() : array
    {
        $pitchStepTable = collect($this->pitchStepTable);
        $pitchStepTableIndex = $pitchStepTable->search($this->pitchStep);
        $pitchStepTableIndex += ($this->pitchAlter ?? 0);

        if ($pitchStepTableIndex < 0) {
            $pitchStep = $pitchStepTable->get($pitchStepTableIndex + $pitchStepTable->count());
            $pitchOctave = $this->pitchOctave - 1;
        } else if ($pitchStepTableIndex >= $pitchStepTable->count()) {
            $pitchStep = $pitchStepTable->get($pitchStepTableIndex - $pitchStepTable->count());
            $pitchOctave = $this->pitchOctave + 1;
        } else {
            $pitchStep = $pitchStepTable->get($pitchStepTableIndex);
            $pitchOctave = $this->pitchOctave;
        }

        return [ $pitchStep, $pitchOctave ];
    }

    public function getNewPitch() : array
    {
        $pitchStep = $this->pitchStep;
        $pitchOctave = $this->pitchOctave;

        list($pitchStep, $pitchOctave) = $this->addPitch($pitchStep, $pitchOctave, $this->getSharpCount());
        list($pitchStep, $pitchOctave) = $this->subPitch($pitchStep, $pitchOctave, $this->getFlatCount());

        return [ $pitchStep, $pitchOctave ];
    }

    public function getSharpCount() : int
    {
        return $this->sharpCount + ($this->hasMajorKey() ? 1 : 0);
    }

    public function getFlatCount() : int
    {
        return $this->flatCount + ($this->hasMinorKey() ? 1 : 0);
    }

    protected function getCurrentKeys() : Collection
    {
        $keys = collect($this->list)->get($this->key);
        return collect($keys);
    }

    protected function hasMajorKey() : bool
    {
        if ($this->key > 0) {
            return $this->getCurrentKeys()->contains($this->pitchStep);
        }
        return false;
    }

    protected function hasMinorKey() : bool
    {
        if ($this->key < 0) {
            return $this->getCurrentKeys()->contains($this->pitchStep);
        }
        return false;
    }

    public function addPitch(string $pitchStep, int $pitchOctave, int $addCount = 1) : array
    {
        for ($i = 0 ; $i < $addCount ; $i++) {
            $pitchTableIndex = collect($this->pitchStepTable)->search(strtolower($pitchStep));
            $pitchTableIndex++;

            if ( ! $pitchStep = collect($this->pitchStepTable)->get($pitchTableIndex)) {
                $pitchStep = 'c';
                $pitchOctave += 1;
            }
        }

        return [ $pitchStep, $pitchOctave ];
    }

    public function subPitch(string $pitchStep, int $pitchOctave, int $subCount = 1) : array
    {
        for ($i = 0 ; $i < $subCount ; $i++) {
            $pitchTableIndex = collect($this->pitchStepTable)->search(strtolower($pitchStep));
            $pitchTableIndex--;

            if ( ! $pitchStep = collect($this->pitchStepTable)->get($pitchTableIndex)) {
                $pitchStep = 'b';
                $pitchOctave -= 1;
            }
        }

        return [ $pitchStep, $pitchOctave ];
    }

}