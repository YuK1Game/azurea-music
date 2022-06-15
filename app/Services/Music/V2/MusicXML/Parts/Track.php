<?php
namespace App\Services\Music\V2\MusicXML\Parts;

use App\Services\Music\V2\MusicXML\Part;
use App\Services\Music\V2\MusicXML\Parts\Measures\Backup;
use App\Services\Music\V2\MusicXML\Parts\Measures\Note;

use Illuminate\Support\Collection;


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