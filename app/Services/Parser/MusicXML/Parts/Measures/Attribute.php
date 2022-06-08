<?php
namespace App\Services\Parser\MusicXML\Parts\Measures;

use App\Services\Parser\MusicXMLChildrenInterface;
use App\Services\Parser\MusicXML\Parts\Measure;
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

    public function getMeasure() : Measure
    {
        return $this->parent;
    }

}