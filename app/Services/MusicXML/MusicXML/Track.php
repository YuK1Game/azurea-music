<?php
namespace App\Services\MusicXML\MusicXML;

use Illuminate\Support\Collection;
use App\Services\MusicXML\MusicXML\TrackNote;

class Track
{
    protected Collection $notes;

    public function __construct(Collection $notesList)
    {
        $this->notes = $notesList->flatten();
    }

    public function trackNotes() : Collection
    {
        return $this->notes->map(function(Note $note, $index) {
            $prevNote = $this->notes->get($index - 1) ?? null;
            return new TrackNote($note, $prevNote);
        });
    }

}