<?php
namespace App\Services\Music\V2\MusicXML\Parts;

use App\Services\Music\V2\MusicXMLChildrenInterface;
use App\Services\Music\V2\MusicXML\Part;
use App\Services\Music\V2\MusicXML\Parts\Tracks\TrackMeasure;

use Illuminate\Support\Collection;
use SimpleXMLElement;


class Track
{
    protected Collection $trackNotes;

    protected Part $parent;

    public function __construct(Collection $trackNotes, $parent)
    {
        $this->trackNotes = $trackNotes;
        $this->parent = $parent;
    }

    public function notes() : Collection
    {
        return $this->trackNotes;
    }

    public function getPart() : Part
    {
        return $this->parent;
    }

}