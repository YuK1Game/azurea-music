<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXMLChildrenInterface;
use App\Services\Music\Parts\Measures\MeasureChildrenInterface;
use App\Services\Music\V2\MusicXML\Parts\Measure;

use Illuminate\Support\Collection;
use SimpleXMLElement;

class Note implements MusicXMLChildrenInterface, MeasureChildrenInterface
{
    protected SimpleXMLElement $xml;

    protected Measure $parent;

    public function __construct(SimpleXMLElement $xml, $parent)
    {
        $this->xml = $xml;
        $this->parent = $parent;
    }

    public function pitchStep() : ?string
    {
        $pitchStep = $this->xml->pitch->step;
        return $pitchStep ? strtolower($pitchStep) : null;
    }

    public function pitchOctave() : ?int
    {
        $pitchOctave = $this->xml->pitch->octave;
        return $pitchOctave ? (int) $pitchOctave : null;
    }

    public function duration() : ?int
    {
        return $this->xml->duration ? (int) $this->xml->duration : null;
    }

    public function isRest() : bool
    {
        return isset($this->xml->rest);
    }

    public function isChord() : bool
    {
        return isset($this->xml->chord);
    }

    public function getMeasure() : Measure
    {
        return $this->parent;
    }

}