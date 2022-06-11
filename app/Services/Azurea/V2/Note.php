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

    protected ?MeasureChildrenInterface $prevMeasureChildren;

    protected ?Collection $currentTrackProperties = null;


    public function __construct(MeasureChildrenInterface $measureChildren)
    {
        $this->measureChildren = $measureChildren;
    }

    public function setPrevNote(?MeasureChildrenInterface $prevMeasureChildren)
    {
        $this->prevMeasureChildren = $prevMeasureChildren;
    }

    public function setCurrentTrackProperties(Collection $currentTrackProperties) : void
    {
        $this->currentTrackProperties = $currentTrackProperties;
    }

    public function getCode() : string
    {
        if ($this->measureChildren instanceof MusicXMLNote) {
            return $this->measureChildren->isRest() ? $this->getRestCode() : $this->getNoteCode();
        }

        if ($this->measureChildren instanceof BlankNote) {
            return $this->getBlankCode();
        }

        if ($this->measureChildren instanceof Backup) {
            return $this->getBackupCode();
        }

        throw new \Exception(sprintf('Invalid class [%s]', get_class($this->measureChildren)));
    }

    public function getRestCode() : string
    {
        return sprintf('r%s', $this->getDurationCode());
    }

    public function getNoteCode() : string
    {
        $key = new Key();
        $key->setPitchStep($this->measureChildren->pitchStep());
        $key->setPitchOctave($this->measureChildren->pitchOctave());
        $key->setKey($this->currentTrackProperties->get('currentKey'));

        list($newPitchStep, $newPitchOctave) = $key->getNewPitch();

        return sprintf('o%d%s%s', $newPitchOctave, $newPitchStep, $this->getDurationCode());
    }

    public function getBlankCode() : string
    {
        return sprintf('r%s', $this->getDurationCode());
    }

    public function getBackupCode() : string
    {
        return '';
    }

    public function getDurationCode() : int
    {
        $duration = new Duration(
            $this->measureChildren->duration(),
            (int) $this->currentTrackProperties->get('currentDivision'),
            (int) $this->currentTrackProperties->get('currentBeatType')
        );

        return $duration->duration();
    }

    public function getSharpCount() : int
    {
        if ($this->measureChildren instanceof MusicXMLNote) {
            switch ($this->measureChildren->accidental()) {
                case 'sharp' : return 1;
                case 'double-sharp' : return 2;
            }
        }
        return 0;
    }

    public function getFlatCount() : int
    {
        if ($this->measureChildren instanceof MusicXMLNote) {
            switch ($this->measureChildren->accidental()) {
                case 'flat' : return 1;
                case 'double-flat' : return 2;
            }
        }
        return 0;
    }

    public function getCurrentMeasureNumber() : int
    {
        $currentMeasure = $this->measureChildren->getMeasure();
        return $currentMeasure->number();
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->{ $name }(...$arguments);
        }
        if (method_exists($this->measureChildren, $name)) {
            return $this->measureChildren->{ $name }(...$arguments);
        }
        throw new \Exception(sprintf('Method not exists. [%s]', $name));
    }

}