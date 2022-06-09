<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXMLChildrenInterface;
use App\Services\Music\V2\MusicXML\Parts\Measure;
use SimpleXMLElement;

class Attribute implements MusicXMLChildrenInterface 
{
    protected SimpleXMLElement $xml;

    protected Measure $parent;

    public function __construct(SimpleXMLElement $xml, $parent)
    {
        $this->xml = $xml;
        $this->parent = $parent;
    }

    public function division() : ?int
    {
        $division = $this->xml->divisions;
        return $division ? (int) $division : null;
    }

    public function beat() : ?int
    {
        $beat = $this->xml->time->beat;
        return $beat ? (int) $beat : null;
    }

    public function beatType() : ?int
    {
        $beatType = $this->xml->time->{'beat-type'};
        return $beatType ? (int) $beatType : null;
    }

    public function getMeasure() : Measure
    {
        return $this->parent;
    }

}