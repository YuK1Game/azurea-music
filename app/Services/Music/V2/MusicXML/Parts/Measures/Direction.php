<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXMLChildrenInterface;
use App\Services\Music\V2\MusicXML\Parts\Measure;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildren;
use SimpleXMLElement;

class Direction extends MeasureChildren implements MusicXMLChildrenInterface, MeasureChildrenInterface
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

    public function dynamics() : ?string
    {
        if (isset($this->xml->{'direction-type'}->dynamics)) {
            $dynamics = $this->xml->{'direction-type'}->dynamics;

            $keys = collect([ 'p', 'mp', 'mf', 'f' ]);

            foreach ($keys as $key) {
                if (isset($dynamics->{ $key })) {
                    return $key;
                }
            }
        }
        return null;
    }

    public function getMeasure() : Measure
    {
        return $this->parent;
    }

}