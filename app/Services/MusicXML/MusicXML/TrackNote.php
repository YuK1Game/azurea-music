<?php
namespace App\Services\MusicXML\MusicXML;

use App\Services\MusicXML\MusicXML\Note;

class TrackNote
{
    protected Note $note;

    protected ?Note $prevNote;

    public function __construct(Note $note, ?Note $prevNote)
    {
        $this->note = $note;
        $this->prevNote = $prevNote;
    }

    public function isChangeOctave() : bool
    {
        if ($this->note->isRest()) {
            return false;
        } else {
            if ($this->prevNote && ! $this->prevNote->isRest()) {
                return $this->note->pitchOctave() !== $this->prevNote->pitchOctave();
            }
            return $this->note->pitchOctave() !== 4;
        }
    }

    public function toAzureaCode() {
        if ($this->isChangeOctave()) {
            return sprintf('%s%d%s', 'o', $this->note->pitchOctave(), $this->getSimpleAzureaCode());
        }
        return $this->getSimpleAzureaCode();
    }

    public function getSimpleAzureaCode() : string
    {
        $duration = $this->note->duration();

        if ($this->note->isRest()) {
            return sprintf('%s%d', 'r', $duration);
        } else {
            return sprintf('%s%d', strtolower($this->note->pitchStep()), $duration);
        }
    }

}