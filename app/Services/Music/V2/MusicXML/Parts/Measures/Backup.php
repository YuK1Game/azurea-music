<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXMLChildrenInterface;
use App\Services\Music\V2\MusicXML\Parts\Measure;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;

use SimpleXMLElement;

class Backup implements MusicXMLChildrenInterface, MeasureChildrenInterface
{
    protected SimpleXMLElement $xml;

    protected Measure $parent;

    public function __construct(SimpleXMLElement $xml, $parent)
    {
        $this->xml = $xml;
        $this->parent = $parent;
    }

    public function duration() : ?int
    {
        return $this->xml->duration ? (int) $this->xml->duration : null;
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

    public function getMeasure() : Measure
    {
        return $this->parent;
    }

}