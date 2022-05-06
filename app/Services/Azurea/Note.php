<?php
namespace App\Services\Azurea;

use App\Services\Music\Parts\Measures\Note as MusicNote;
use App\Services\Azurea\Notes\NotePitchTable;
use App\Services\Music\Parts\Measures\MeasureKey;

class Note
{
    protected ?MusicNote $musicNote;

    protected int $baseDuration = 0;

    protected ?int $noteDuration = null;

    protected ?Note $prevNote;

    protected int $sharpCount;

    protected int $flatCount;

    protected MeasureKey $measureKey;

    public function __construct(?MusicNote $musicNote, int $measureTotalDuration)
    {
        $this->musicNote = $musicNote;
        $this->measureTotalDuration = $measureTotalDuration;

        $this->sharpCount = ($this->musicNote && $this->musicNote->isSharp()) ? 1 : 0;
        $this->flatCount = ($this->musicNote && $this->musicNote->isFlat()) ? 1 : 0;
    }

    public function setNoteDuration(int $noteDuration) : void
    {
        $this->noteDuration = $noteDuration;
    }

    public function getNoteDuration() : ?int
    {
        return $this->noteDuration;
    }

    public function setPrevNote(?Note $prevNote) : void
    {
        $this->prevNote = $prevNote;
    }

    public function setMeasureKey(MeasureKey $measureKey) : void
    {
        $this->measureKey = $measureKey;
    }

    public function addSharpCount(int $count = 1) : void
    {
        $this->sharpCount += $count;
    }

    public function addFlatCount(int $count = 1) : void
    {
        $this->flatCount += $count;
    }

    public function isBlank() : bool
    {
        return ! $this->musicNote;
    }

    public function isSamePitchPrevNote() : bool
    {
        return $this->prevNote && $this->prevNote->pitch() === $this->pitch();
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
        
        $pitchCode = sprintf('o%d%s', $pitchOctave, $pitchStep);
        $step = sprintf('%s%s', $pitchCode, $this->duration());

        if ($this->musicNote->isChord()) {
            $step = ':' . $step;
        }

        if ($this->musicNote->isTieEnd() && $this->isSamePitchPrevNote()) {
            return $this->musicNote->isChord() ? '' : $this->rest();
        }

        return $step;
    }

    public function pitch() : array
    {
        $pitchStep = $this->musicNote->pitchStep();
        $pitchOctave = $this->musicNote->pitchOctave();

        if ( ! $this->musicNote->isNatural()) {
            list($pitchStep, $pitchOctave) = NotePitchTable::addPitch($pitchStep, $pitchOctave, $this->getSharpCount());
            list($pitchStep, $pitchOctave) = NotePitchTable::subPitch($pitchStep, $pitchOctave, $this->getFlatCount());
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

    protected function getSharpCount() : int
    {
        $count = $this->musicNote->isSharp() ? 1 : 0;
        $count += $this->measureKey->isSharp($this->musicNote->pitchStep()) ? 1 : 0;
        return $count;
    }

    protected function getFlatCount() : int
    {
        $count = $this->musicNote->isFlat() ? 1 : 0;
        $count += $this->measureKey->isFlat($this->musicNote->pitchStep()) ? 1 : 0;
        return $count;
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

}