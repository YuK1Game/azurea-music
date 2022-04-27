<?php
namespace App\Services\MusicXML\MusicXML;

use App\Services\MusicXML\MusicXML\Note;

class TrackNote
{
    public Note $note;

    public ?Note $prevNote;

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
        $code = '';

        if ($this->note->isChord()) {
            $code .= ':';
        }

        if ($this->note->isFlat()) {
            $this->note->subLevel();
        }

        if ($this->note->isSharp()) {
            $this->note->addLevel();
        }

        if ($this->isChangeOctave()) {
            $code .= sprintf('%s%d', 'o', $this->note->pitchOctave());
        }

        // echo $this->note->toXml() . PHP_EOL;
        // echo $this->note->duration() . PHP_EOL;
        
        return $code .= $this->getSimpleAzureaCode();
    }

    public function getAzureaDurationCode() : string
    {
        $noteDuration = $this->note->duration();
        $baseDuration = (int) pow(2, floor(log($noteDuration, 2)));
        $hasDot = $noteDuration !== $baseDuration;
        $duration = $baseDuration > 0 ? (int) 32 / $baseDuration : 0;

        return sprintf('%d%s', $duration, $hasDot ? '.' : '');
    }

    public function getSimpleAzureaCode() : string
    {
        if ($this->note->isRest()) {
            return sprintf('%s%s', 'r', $this->getAzureaDurationCode());
        }
        return sprintf('%s%s', strtolower($this->note->pitchStep()), $this->getAzureaDurationCode());
    }

}