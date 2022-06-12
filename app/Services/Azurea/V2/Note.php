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

    public function getCode() : string
    {
        $measureChildren = $this->measureChildren;

        if ($measureChildren instanceof MusicXMLNote) {
            if ($this->isTieEnded()) {
                return sprintf('%s%s', $this->getRestCode(), $this->getDurationCode());
            }
            return sprintf('%s%s%s', $this->getMusicXMLNote()->isChord() ? ':' : '', $this->getNoteCode(), $this->getDurationCode());
        }

        if ($measureChildren instanceof BlankNote) {
            return sprintf('%s%s', $this->getBlankCode(), $this->getDurationCode());
        }

        if ($measureChildren instanceof Backup) {
            return $this->getBackupCode();
            return sprintf('%s%s', $this->getBlankCode(), $this->getDurationCode());
        }

        throw new \Exception(sprintf('Invalid class [%s]', get_class($measureChildren)));
    }

    public function getNoteCode() : string
    {
        return $this->getMusicXMLNote()->isRest() ? $this->getRestCode() : $this->getPhonicNoteCode();
    }

    public function getRestCode() : string
    {
        return 'r';
    }

    public function getPhonicNoteCode() : string
    {
        return $this->isNatural() ? $this->getNaturalNoteCode() : $this->getScaleAdjustmentsNoteCode();
    }

    public function getPitch() : string
    {
        return sprintf('o%d%s', $this->getMusicXMLNote()->pitchOctave(), $this->getMusicXMLNote()->pitchStep());
    }

    protected function getScaleAdjustmentsNoteCode() : string
    {
        $pitchStep = $this->measureChildren->pitchStep();
        $pitchOctave = $this->measureChildren->pitchOctave();

        $key = new Key();
        $key->setPitchStep($pitchStep);
        $key->setPitchOctave($pitchOctave);
        $key->setSharpCount($this->getSharpCount());
        $key->setFlatCount($this->getFlatCount());
        $key->setKey($this->currentTrackProperties->get('currentKey'));

        list($newPitchStep, $newPitchOctave) = $key->getNewPitch();

        return sprintf('o%d%s', $newPitchOctave, $newPitchStep);
    }

    protected function getNaturalNoteCode() : string
    {
        $pitchStep = $this->measureChildren->pitchStep();
        $pitchOctave = $this->measureChildren->pitchOctave();
        return sprintf('o%d%s', $pitchStep, $pitchOctave);
    }

    protected function getBlankCode() : string
    {
        return 'r';
    }

    protected function getBackupCode() : string
    {
        return '';
    }

    protected function getDurationCode() : string
    {
        $duration = new Duration(
            $this->measureChildren->duration(),
            (int) $this->currentTrackProperties->get('currentDivision'),
            (int) $this->currentTrackProperties->get('currentBeatType')
        );

        switch($duration->dotCount()) {
            case 1 : return sprintf('%s.', $duration->duration());
            case 2 : return sprintf('%s.r%s', $duration->duration(), $duration->duration() / 4);
            default : return sprintf('%s', $duration->duration());
        }
    }

    protected function getSharpCount() : int
    {
        switch ($this->getMusicXMLNote()->accidental()) {
            case 'sharp' : return 1;
            case 'double-sharp' : return 2;
            default : return 0;
        }
    }

    protected function getFlatCount() : int
    {
        switch ($this->getMusicXMLNote()->accidental()) {
            case 'flat' : return 1;
            case 'double-flat' : return 2;
            default : return 0;
        }
    }

    protected function isNatural() : bool
    {
        return $this->getMusicXMLNote()->isNatural();
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