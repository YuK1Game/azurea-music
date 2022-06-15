<?php
namespace App\Services\Azurea\V2;

use App\Services\Music\V2\MusicXML\Parts\Measure as MusicXMLMeasure;
use App\Services\Music\V2\MusicXML\Parts\Measures\Note as MusicXMLNote;
use App\Services\Music\V2\MusicXML\Parts\Measures\Backup;
use App\Services\Music\V2\MusicXML\Parts\Measures\BlankNote;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;
use Illuminate\Support\Collection;

use App\Services\Azurea\V2\Track as AzureaTrack;
use App\Services\Azurea\V2\Notes\{ Duration, Key };

class Note
{
    protected MeasureChildrenInterface $measureChildren;

    protected AzureaTrack $azureaTrack;

    protected ?Note $prevAzureaNote;

    protected ?Collection $currentTrackProperties = null;


    public function __construct(MeasureChildrenInterface $measureChildren, AzureaTrack $azureaTrack)
    {
        $this->measureChildren = $measureChildren;

        $this->azureaTrack = $azureaTrack;
    }

    public function setPrevAzureaNote(?Note $prevAzureaNote)
    {
        $this->prevAzureaNote = $prevAzureaNote;
    }

    public function setCurrentTrackProperties(Collection $currentTrackProperties) : void
    {
        $this->currentTrackProperties = $currentTrackProperties;
    }

    public function index() : ?int
    {
        return $this->getMusicXMLNote() && $this->getMusicXMLNote()->index();
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
            $extraDuration = $this->getWholeDuration() - $this->measureChildren->duration();

            if ($extraDuration > 0) {
                $duration = $this->createDuration($extraDuration);
                return sprintf('r%s%s', $duration->duration(), str_repeat('.', $duration->dotCount()));
            }
            return '';
        }

        throw new \Exception(sprintf('Invalid class [%s]', get_class($measureChildren)));
    }

    public function getNoteCode() : string
    {
        $pitch =  $this->getMusicXMLNote()->isRest() ? 'r' : $this->getPhonicNotePitch();
        $code = sprintf('%s%s', $pitch, $this->getDurationCode());

        if ($this->isChord()) {
            $code = sprintf(':%s', $code);
        }

        if ($this->isAccent()) {
            $code = sprintf('%s*14', $code);
        }

        if ($this->isStaccato()) {
            $code = sprintf('%s*13', $code);
        }

        if ($this->isChord() && $this->isTieEnd()) {
            return '';
        }

        return $code;
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

    public function getDurationCode() : string
    {
        $duration = $this->createDuration($this->measureChildren->duration());
        return sprintf('%s%s', $duration->duration(), str_repeat('.', $duration->dotCount()));
    }

    protected function createDuration(int $duration) : Duration
    {
        return new Duration(
            $duration,
            (int) $this->currentTrackProperties->get('currentDivision'),
            (int) $this->currentTrackProperties->get('currentBeatType')
        );
    }
    
    public function isChord() : bool
    {
        return $this->isMusicXMLNote() && $this->getMusicXMLNote()->isChord();
    }

    public function isAccent() : bool
    {
        return $this->isMusicXMLNote() && $this->getMusicXMLNote()->accent();
    }

    public function isStaccato() : bool
    {
        return $this->isMusicXMLNote() && $this->getMusicXMLNote()->staccato();
    }

    public function isTieStart() : bool
    {
        return $this->isMusicXMLNote() && $this->getMusicXMLNote()->isTieStart();
    }

    public function isTieEnd() : bool
    {
        return $this->isMusicXMLNote() && $this->getMusicXMLNote()->isTieEnd();
    }
    
    protected function getWholeDuration() : int
    {
        $currentDivision = (int) $this->currentTrackProperties->get('currentDivision');
        $currentBeatType = (int) $this->currentTrackProperties->get('currentBeatType');
        return $currentDivision * $currentBeatType;
    }

    protected function isMusicXMLNote() : bool
    {
        return $this->measureChildren instanceof MusicXMLNote;
    }

    protected function defaultPitch() : ?string
    {
        if ($this->isMusicXMLNote()) {
            return sprintf('o%d%s', $this->getMusicXMLNote()->pitchOctave(), $this->getMusicXMLNote()->pitchStep());
        }
        return null;
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