<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;

use SimpleXMLElement;

abstract class MeasureChildren implements MeasureChildrenInterface
{
    public function duration() : ?int
    {
        return null;
    }

    public function pitchStep() : ?string
    {
        return '';
    }

    public function pitchOctave() : ?int
    {
        return '';
    }

    public function pitchAlter() : ?int
    {
        return null;
    }

    public function hasUnpitched() : bool
    {
        return false;
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

    public function getXml() : ?SimpleXMLElement
    {
        return $this->xml;
    }
}
