<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXML\Parts\Measure;
use App\Services\Music\V2\MusicXML\Parts\Measures\Note;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildren;

use Illuminate\Support\Collection;
use SimpleXMLElement;

class BlankNote extends MeasureChildren implements MeasureChildrenInterface
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

    public function getMeasure() : Measure
    {
        return $this->parent;
    }

    public function getXml() : ?SimpleXMLElement
    {
        return null;
    }

}
