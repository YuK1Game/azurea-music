<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXMLChildrenInterface;
use App\Services\Music\V2\MusicXML\Parts\Measure;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;

use SimpleXMLElement;

abstract class MeasureChildren implements MusicXMLChildrenInterface, MeasureChildrenInterface
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

    public function getXml() : ?SimpleXMLElement
    {
        return $this->xml;
    }
}