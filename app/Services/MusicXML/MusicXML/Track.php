<?php
namespace App\Services\MusicXML\MusicXML;

use Illuminate\Support\Collection;
use App\Services\MusicXML\MusicXML\TrackNote;

class Track
{
    protected Collection $notesList;

    public function __construct(Collection $notesList)
    {
        $this->notesList = $notesList;
    }

    public function trackNotes() : Collection
    {
        $prevNote = null;

        return $this->notesList->map(function(Collection $notes) use($prevNote) {
            return $notes->map(function(Note $note) use($prevNote) {
                $trackNote = new TrackNote($note, $prevNote);
                $prevNote = $note;
                return $trackNote;
            });
        });
    }

}