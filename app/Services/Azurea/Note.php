<?php
namespace App\Services\Azurea;

use App\Services\Music\Parts\Measures\Note as MusicNote;
use App\Services\Azurea\Notes\NotePitchTable;

class Note
{
    protected MusicNote $musicNote;

    protected int $baseDuration = 0;

    public function __construct(MusicNote $musicNote, int $baseDuration)
    {
        $this->musicNote = $musicNote;
        $this->baseDuration = $baseDuration;
    }

    public function code()
    {
        return $this->musicNote->isRest() ? $this->rest() : $this->step();
    }

    public function rest() : string
    {
        return sprintf('r%s', $this->duration());
    }

    public function step() : string
    {
        list($pitchStep, $pitchOctave) = $this->pitch();

        $step = sprintf('o%d%s%s', $pitchOctave, $pitchStep, $this->duration());

        if ($this->musicNote->isChord()) {
            $step = ':' . $step;
        }

        return $step;
    }

    public function pitch() : array
    {
        $pitchStep = $this->musicNote->pitchStep();
        $pitchOctave = $this->musicNote->pitchOctave();

        if ($this->musicNote->isFlat()) {
            list($pitchStep, $pitchOctave) = NotePitchTable::subPitch($pitchStep, $pitchOctave);
        }

        if ($this->musicNote->isSharp()) {
            list($pitchStep, $pitchOctave) = NotePitchTable::addPitch($pitchStep, $pitchOctave);
        }

        return [ $pitchStep, $pitchOctave ];
    }

    public function duration() : string
    {
        $baseDuration = (string) $this->baseDuration();

        if ($this->isDottedDuration()) {
            $baseDuration .= '.';
        }

        return $baseDuration;
    }

    public function baseDuration() : int
    {
        $overDuration = $this->baseDuration % $this->musicNote->duration();
        $duration = $this->baseDuration / ($this->musicNote->duration() - $overDuration);
        return $duration;
    }

    public function isDottedDuration() : bool
    {
        return $this->baseDuration % $this->musicNote->duration() > 0;
    }

}