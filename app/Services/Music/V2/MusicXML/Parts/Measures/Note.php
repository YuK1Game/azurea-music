<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXMLChildrenInterface;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;
use App\Services\Music\V2\MusicXML\Parts\Measure;

use Illuminate\Support\Collection;
use SimpleXMLElement;

class Note implements MeasureChildrenInterface
{
    protected SimpleXMLElement $xml;

    protected Measure $parent;

    protected int $noteIndex;

    public function __construct(SimpleXMLElement $xml, $parent, int $noteIndex)
    {
        $this->xml = $xml;
        $this->parent = $parent;
        $this->noteIndex = $noteIndex;
    }

    public function index() : int
    {
        return $this->noteIndex;
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

    public function pitchAlter() : ?int
    {
        $pitchAlter = $this->xml->pitch->alter;
        return $pitchAlter ? (int) $pitchAlter : null;
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

    public function accidental() : ?string
    {
        return $this->xml->accidental ? (string) $this->xml->accidental : null;
    }

    public function isSharp() : bool
    {
        return $this->accidental() === 'sharp';
    }

    public function isDoubleSharp() : bool
    {
        return $this->accidental() === 'double-sharp';
    }

    public function isFlat() : bool
    {
        return $this->accidental() === 'flat';
    }

    public function isDoubleFlat() : bool
    {
        return $this->accidental() === 'double-flat';
    }

    public function isNatural() : bool
    {
        return $this->accidental() === 'natural';
    }

    public function tieType() : ?string
    {
        return $this->xml->notations->tied ? $this->xml->notations->tied['type'] : null;
    }

    public function isTieStart() : bool
    {
        return 'start' === $this->tieType();
    }

    public function isTieEnd() : bool
    {
        return 'stop' === $this->tieType();
    }

    public function getMeasure() : Measure
    {
        return $this->parent;
    }

}