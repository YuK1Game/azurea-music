<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXML\Parts\Measure;
use App\Services\Music\V2\MusicXML\Parts\Measures\Note;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;

use Illuminate\Support\Collection;
use SimpleXMLElement;

class BlankNote implements MeasureChildrenInterface
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

    public function duration() : int
    {
        $tracks = $this->getMeasure()->getDividedTracks()->get(0);

        return $tracks->sum(function($note) {
            if ($note instanceof Note) {
                return $note->isChord() ? 0 : $note->duration();
            }
            return 0;
        });
    }

    public function pitchStep() : ?string
    {
        return '';
    }

    public function pitchOctave() : ?int
    {
        return '';
    }

    public function pitchAlter() : ?int
    {
        return null;
    }

    public function hasUnpitched() : bool
    {
        return false;
    }

    public function getMeasure() : Measure
    {
        return $this->parent;
    }

    public function getXml() : ?SimpleXMLElement
    {
        return null;
    }

}