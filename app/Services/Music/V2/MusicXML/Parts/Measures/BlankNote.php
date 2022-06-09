<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXMLChildrenInterface;
use App\Services\Music\V2\MusicXML\Parts\Measure;

use Illuminate\Support\Collection;
use SimpleXMLElement;

class BlankNote
{
    protected SimpleXMLElement $xml;

    protected Measure $parent;

    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    public function isRest() : bool
    {
        return true;
    }

    public function getMeasure() : Measure
    {
        return $this->parent;
    }

}