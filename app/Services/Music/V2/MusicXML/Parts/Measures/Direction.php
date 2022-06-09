<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXMLChildrenInterface;
use App\Services\Music\V2\MusicXML\Parts\Measure;
use SimpleXMLElement;

class Direction implements MusicXMLChildrenInterface 
{
    protected SimpleXMLElement $xml;

    protected Measure $parent;

    public function __construct(SimpleXMLElement $xml, $parent)
    {
        $this->xml = $xml;
        $this->parent = $parent;
    }

    public function tempo() : ?int
    {
        if (isset($this->xml->sound['tempo'])) {
            return (int) $this->xml->sound['tempo'];
        }
        return null;
    }

    public function getMeasure() : Measure
    {
        return $this->parent;
    }

}