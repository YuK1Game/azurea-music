<?php
namespace App\Services\Parser\MusicXML\Parts;

use App\Services\Parser\MusicXMLChildrenInterface;
use App\Services\Parser\MusicXML\Part;
use App\Services\Parser\MusicXML\Parts\Tracks\TrackMeasure;

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