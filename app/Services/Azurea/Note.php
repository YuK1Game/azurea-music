<?php
namespace App\Services\Azurea;

use App\Services\Music\Parts\Measures\Note as MusicNote;

class Note
{
    protected MusicNote $musicNote;

    protected int $baseDuration = 48;

    public function __construct(MusicNote $musicNote)
    {
        $this->musicNote = $musicNote;
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
        $step = sprintf('o%d%s%s', $this->musicNote->pitchOctave(), $this->musicNote->pitchStep(), $this->duration());

        if ($this->musicNote->isChord()) {
            $step = ':' . $step;
        }

        return $step;
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
        switch ($this->musicNote->duration()) {
            case 48 : return  1;
            case 36 :
            case 24 : return  2;
            case 18 :
            case 12 : return  4;
            case  9 : 
            case  6 : return  8;
            case  4 : return 12;
            case  3 : return 16;
            case  2 : return 32;

            default:
                throw new \Exception(sprintf('Duration error. [%d]', $this->musicNote->duration()));
        }
    }

    public function isDottedDuration() : bool
    {
        return $this->baseDuration % $this->musicNote->duration() > 0;
    }

}