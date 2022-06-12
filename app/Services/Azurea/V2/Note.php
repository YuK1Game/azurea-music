<?php
namespace App\Services\Azurea\V2;

use App\Services\Music\V2\MusicXML\Parts\Measures\Note as MusicXMLNote;
use App\Services\Music\V2\MusicXML\Parts\Measures\Backup;
use App\Services\Music\V2\MusicXML\Parts\Measures\BlankNote;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;
use Illuminate\Support\Collection;

use App\Services\Azurea\V2\Notes\{ Duration, Key };

class Note
{
    protected MeasureChildrenInterface $measureChildren;

    protected ?Note $prevAzureaNote;

    protected ?Collection $currentTrackProperties = null;

    protected ?string $accidental = null;


    public function __construct(MeasureChildrenInterface $measureChildren)
    {
        $this->measureChildren = $measureChildren;
    }

    public function setPrevAzureaNote(?Note $prevAzureaNote)
    {
        $this->prevAzureaNote = $prevAzureaNote;
    }

    public function setCurrentTrackProperties(Collection $currentTrackProperties) : void
    {
        $this->currentTrackProperties = $currentTrackProperties;
    }

    public function setAccidental(?string $accidental) {
        $this->accidental = $accidental;
    }

    public function getCode() : string
    {
        $measureChildren = $this->measureChildren;

        if ($measureChildren instanceof MusicXMLNote) {
            return $this->getNoteCode();
        }

        if ($measureChildren instanceof BlankNote) {
            return sprintf('r%s', $this->getDurationCode());
        }

        if ($measureChildren instanceof Backup) {
            return '';
        }

        throw new \Exception(sprintf('Invalid class [%s]', get_class($measureChildren)));
    }

    public function getNoteCode() : string
    {
        $code = $this->getMusicXMLNote()->isRest() ? 'r' : $this->getPhonicNotePitch();

        if ($this->getMusicXMLNote()->isTieEnd()) {
            if ($this->isChord()) {
                return '';
            }
            return sprintf('r%s', $this->getDurationCode());
        }

        if ($this->isChord()) {
            return sprintf(':%s%s', $code, $this->getDurationCode());
        }

        return sprintf('%s%s', $code, $this->getDurationCode());
    }

    public function getPitch() : string
    {
        return sprintf('o%d%s', $this->getMusicXMLNote()->pitchOctave(), $this->getMusicXMLNote()->pitchStep());
    }

    protected function getPhonicNotePitch() : string
    {
        $key = new Key();
        $key->setPitchStep($this->measureChildren->pitchStep());
        $key->setPitchOctave($this->measureChildren->pitchOctave());
        $key->setPitchAlter($this->measureChildren->pitchAlter());

        list($newPitchStep, $newPitchOctave) = $key->newPitch();

        return sprintf('o%d%s', $newPitchOctave, $newPitchStep);
    }

    protected function getBlankCode() : string
    {
        return 'r';
    }

    protected function getBackupCode() : string
    {
        return '';
    }

    public function getDurationManager() : Duration
    {
        return new Duration(
            $this->measureChildren->duration(),
            (int) $this->currentTrackProperties->get('currentDivision'),
            (int) $this->currentTrackProperties->get('currentBeatType')
        );
    }

    public function getDurationCode() : string
    {
        $duration = $this->getDurationManager();

        switch($duration->dotCount()) {
            case 1 : return sprintf('%s.', $duration->duration());
            case 2 : return sprintf('%s.r%s', $duration->duration(), $duration->duration() * 4);
            default : return sprintf('%s', $duration->duration());
        }
    }

    protected function isTieEnded() : bool
    {
        if ($this->getMusicXMLNote()->isTieEnd()) {
            if ($prevAzureaNote = $this->prevAzureaNote) {
                if ($this->isMusicXMLNote() && $prevAzureaNote->isMusicXMLNote()) {
                    return $prevAzureaNote->getNoteCode() === $this->getNoteCode();
                }
            }
            return false;
        }
        return false;
    }

    public function isChord() : bool
    {
        if ($this->isMusicXMLNote()) {
            return $this->getMusicXMLNote()->isChord();
        }
        return false;
    }

    public function isMusicXMLNote() : bool
    {
        return $this->measureChildren instanceof MusicXMLNote;
    }

    public function getMusicXMLNote() : MusicXMLNote
    {
        if ($this->isMusicXMLNote()) {
            return $this->measureChildren;
        }
        throw new \Exception('Not a MusicXMLNote Object.');
    }

    public function getCurrentMeasureNumber() : int
    {
        $currentMeasure = $this->measureChildren->getMeasure();
        return $currentMeasure->number();
    }

}