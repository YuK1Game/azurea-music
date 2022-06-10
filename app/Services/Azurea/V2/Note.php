<?php
namespace App\Services\Azurea\V2;

use App\Services\Music\V2\MusicXML\Parts\Measures\Note as MusicXMLNote;
use App\Services\Music\V2\MusicXML\Parts\Measures\Backup;
use App\Services\Music\V2\MusicXML\Parts\Measures\BlankNote;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;
use Illuminate\Support\Collection;

use App\Services\Azurea\V2\Notes\Duration;

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
        return sprintf('%s%s', $this->measureChildren->pitchStep(), $this->getDurationCode());
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

    public function getCurrentMeasureNumber() : int
    {
        $currentMeasure = $this->measureChildren->getMeasure();
        return $currentMeasure->number();
    }

}