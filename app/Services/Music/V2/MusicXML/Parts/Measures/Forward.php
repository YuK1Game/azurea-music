<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXMLChildrenInterface;
use App\Services\Music\V2\MusicXML\Parts\Measure;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildren;

use SimpleXMLElement;

class Forward extends MeasureChildren implements MusicXMLChildrenInterface, MeasureChildrenInterface
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

    public function getMeasure() : Measure
    {
        return $this->parent;
    }

    public function getXml() : ?SimpleXMLElement
    {
        return $this->xml;
    }

}
