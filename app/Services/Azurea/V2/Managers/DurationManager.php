<?php
namespace App\Services\Azurea\V2\Managers;

use App\Services\Azurea\V2\Note;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;

class DurationManager
{
    protected Note $azureaNote;

    protected MeasureChildrenInterface $measureChildren;

    public function __construct(Note $azureaNote)
    {
        $this->azureaNote = $azureaNote;
        $this->measureChildren = $azureaNote->getMeasureChildren();
    }

    public function getWholeDuration() : int
    {
        return $this->azureaNote->getWholeDuration();
    }

    public function naturalDuration() : ?int
    {
        return $this->measureChildren->duration();
    }

    public function isNaturalDuration() : bool
    {
        return $this->getWholeDuration() % $this->duration === 0;
    }

    public function hasDurationCode() : bool
    {
        return true;
    }

    public function getDurationCode() : string
    {
        return '1';
    }

}
