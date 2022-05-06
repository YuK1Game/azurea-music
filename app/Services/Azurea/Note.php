<?php
namespace App\Services\Azurea;

use App\Services\Music\Parts\Measures\Note as MusicNote;
use App\Services\Azurea\Notes\NotePitchTable;

class Note
{
    protected ?MusicNote $musicNote;

    protected int $baseDuration = 0;

    protected ?int $noteDuration = null;

    public function __construct(?MusicNote $musicNote, int $measureTotalDuration)
    {
        $this->musicNote = $musicNote;
        $this->measureTotalDuration = $measureTotalDuration;
    }

    public function setNoteDuration(int $noteDuration) : void
    {
        $this->noteDuration = $noteDuration;
    }

    public function getNoteDuration() : ?int
    {
        return $this->noteDuration;
    }

    public function isBlank() : bool
    {
        return ! $this->musicNote;
    }

    public function code()
    {
        return $this->isBlank() || $this->musicNote->isRest() ? $this->rest() : $this->step();
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
        list($baseDuration, $isDottedDuration) = $this->baseDuration();

        $baseDuration = $baseDuration;

        if ($isDottedDuration) {
            $baseDuration .= '.';
        }

        return $baseDuration;
    }

    protected function baseDuration() : array
    {
        $noteDuration = $this->getNoteDuration() ?? $this->musicNote->duration();
        $x = $noteDuration / $this->measureTotalDuration;
        $y = fmod(1, $x);

        $isDottedDuration = $y > 0;
        $duration = 1 / ($isDottedDuration ? ($x / 1.5) : $x);

        return [ $duration, $isDottedDuration ];
    }

    public function debugCode() : string
    {
        if ($this->musicNote->isChord()) {
            return '';
        }
        return sprintf('%d [%s]', $this->musicNote->duration(), $this->duration());
    }

}