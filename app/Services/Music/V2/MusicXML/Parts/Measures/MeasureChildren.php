<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;

use SimpleXMLElement;

abstract class MeasureChildren implements MeasureChildrenInterface
{
    public function index() : ?int
    {
        return null;
    }

    public function duration() : ?int
    {
        return null;
    }

    public function pitchStep() : ?string
    {
        return null;
    }

    public function pitchOctave() : ?int
    {
        return null;
    }

    public function pitchAlter() : ?int
    {
        return null;
    }

    public function isTuplet() : bool
    {
        return false;
    }

    public function tupletActualNotes() : ?int
    {
        return null;
    }

    public function tupletNormalNotes() : ?int
    {
        return null;
    }

    public function isRest(): bool
    {
        return false;
    }

    public function isChord() : bool
    {
        return false;
    }

    public function grace() : bool
    {
        return false;
    }

    public function accent() : bool
    {
        return false;
    }

    public function staccato() : bool
    {
        return false;
    }

    public function arpeggiate(): bool
    {
        return false;
    }

    public function isTieStart() : bool
    {
        return false;
    }

    public function isTieEnd() : bool
    {
        return false;
    }

    public function dynamics(): ?string
    {
        return null;
    }

    public function isNote() : bool
    {
        return $this instanceof Note;
    }

    public function isDirection() : bool
    {
        return $this instanceof Direction;
    }

    public function isBackup() : bool
    {
        return $this instanceof Backup;
    }

    public function isBlankNote() : bool
    {
        return $this instanceof BlankNote;
    }

    public function isForward() : bool
    {
        return $this instanceof Forward;
    }

    public function hasPitch() : bool
    {
        return $this->isNote()
            && ! $this->isRest();
    }

    public function hasUnpitched() : bool
    {
        return false;
    }

    public function getXml() : ?SimpleXMLElement
    {
        return $this->xml;
    }
}
