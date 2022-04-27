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

        return $this->notesList->map(function(Collection $notes) use(&$prevNote) {

            $flats = collect();
            $sharps = collect();

            return $notes->map(function(Note $note) use(&$prevNote, &$flats, &$sharps) {
                $note->isFlat() &&  $flats->push($note->getCode());
                $note->isSharp() &&  $sharps->push($note->getCode());
                
                $flats->contains($note->getCode()) && $note->setFlat();
                $sharps->contains($note->getCode()) && $note->setSharp();

                $trackNote = new TrackNote($note, $prevNote);

                $prevNote = $note;
                return $trackNote;
            });
        });
    }

}