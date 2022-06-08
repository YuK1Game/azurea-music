<?php
namespace App\Services\Parser\MusicXML\Parts\Measures;

use App\Services\Parser\MusicXMLChildrenInterface;
use App\Services\Parser\MusicXML\Parts\Measure;
use SimpleXMLElement;

class Backup implements MusicXMLChildrenInterface 
{
    protected SimpleXMLElement $xml;

    protected Measure $parent;

    public function __construct(SimpleXMLElement $xml, $parent)
    {
        $this->xml = $xml;
        $this->parent = $parent;
    }

    public function getMeasure() : Measure
    {
        return $this->parent;
    }

}