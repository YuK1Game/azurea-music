<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildren;
use App\Services\Music\V2\MusicXML\Parts\Measure;
use Illuminate\Support\Collection;
use SimpleXMLElement;

class Note extends MeasureChildren implements MeasureChildrenInterface
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

    public function unpitchedStep() : ?string
    {
        $pitchStep = $this->xml->unpitched->{'display-step'};
        return $pitchStep ? strtolower($pitchStep) : null;
    }

    public function unpitchedOctave() : ?int
    {
        $pitchOctave = $this->xml->unpitched->{'display-octave'};
        return $pitchOctave ? (int) $pitchOctave : null;
    }

    public function hasUnpitched() : bool
    {
        return isset($this->xml->unpitched);
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

    public function accent() : bool
    {
        return isset($this->xml->notations->articulations->accent);
    }

    public function staccato() : bool
    {
        return isset($this->xml->notations->articulations->staccato);
    }

    public function grace() : bool
    {
        return isset($this->xml->grace);
    }

    public function type() : ?string
    {
        return $this->xml->type ?? null;
    }

    public function tieTypes() : Collection
    {
        $types = collect([]);

        if (isset($this->xml->tie)) {
            foreach ($this->xml->tie as $tie) {
                $types->push($tie['type']);
            }
        }

        return $types;
    }

    public function isTieStart() : bool
    {
        return $this->tieTypes()->contains('start');
    }

    public function isTieEnd() : bool
    {
        return $this->tieTypes()->contains('stop');
    }

    public function isTuplet() : bool
    {
        return isset($this->xml->{'time-modification'});
    }

    public function tupletActualNotes() : ?int
    {
        if ($this->isTuplet()) {
            return (int) $this->xml->{'time-modification'}->{'actual-notes'};
        }
        return null;
    }

    public function tupletNormalNotes() : ?int
    {
        if ($this->isTuplet()) {
            return (int) $this->xml->{'time-modification'}->{'normal-notes'};
        }
        return null;
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
