<?php
namespace App\Services\Azurea\V2\Managers;

use App\Services\Azurea\V2\Managers\DurationManagers\DurationTable;
use App\Services\Azurea\V2\Note;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;

use App\Services\Music\V2\MusicXML\Parts\Measures\Note as MusicXMLNote;
use App\Services\Music\V2\MusicXML\Parts\Measures\Direction as MusicXMLDirection;
use App\Services\Music\V2\MusicXML\Parts\Measures\Backup as MusicXMLBackup;
use App\Services\Music\V2\MusicXML\Parts\Measures\BlankNote as MusicXMLBlankNote;
use Illuminate\Support\Collection;

use DivisionByZeroError;
use Exception;

class DurationManager
{
    protected Note $azureaNote;

    protected MeasureChildrenInterface $measureChildren;

    public function __construct(Note $azureaNote)
    {
        $this->azureaNote = $azureaNote;
        $this->measureChildren = $azureaNote->getMeasureChildren();
    }

    public function getWholeDuration() : int
    {
        return $this->azureaNote->getWholeDuration();
    }

    public function getDuration() : ?float
    {
        if ($this->isBackup()) {
            return $this->getWholeDuration() - $this->measureChildren->duration();
        }
        return $this->azureaNote->getCustomDuration() ?? $this->measureChildren->duration();
    }

    public function isCustomDuration() : bool
    {
        return !! $this->azureaNote->getCustomDuration();
    }

    public function isNote() : bool
    {
        return $this->measureChildren instanceof MusicXMLNote;
    }

    public function isBlankNote() : bool
    {
        return $this->measureChildren instanceof MusicXMLBlankNote;
    }

    public function isBackup() : bool
    {
        return $this->measureChildren instanceof MusicXMLBackup;
    }

    public function isDirection() : bool
    {
        return $this->measureChildren instanceof MusicXMLDirection;
    }

    public function getDurationCodes() : ?Collection
    {
        try {
            return $this->getBaseDurationAndDotCount();

        } catch (Exception $e) {
            $this->throwDurationManagerException($e->getMessage());

        } catch (DivisionByZeroError $e) {
            $this->throwDurationManagerException($e->getMessage());
        }

    }

    public function getBaseDurationAndDotCount() : ?Collection
    {
        $durationTable = new DurationTable($this->getWholeDuration());
        if ($durations = $durationTable->getDurationListByDuration($this->getDuration())) {
            return $durations;
        }
        return null;
    }

    protected function throwDurationManagerException(string $message) : void
    {
        $errorJson = [
            'measure_number' => $this->azureaNote->getCurrentMeasureNumber(),
            'durations' => [
                'whole_duration' => $this->getWholeDuration(),
                'duration' => $this->getDuration(),
                'is_custom_duration' => $this->isCustomDuration(),
            ],
            'note' => [
                'class' => get_class($this->measureChildren),
                'xml' => $this->measureChildren->getXml(),
            ],
        ];

        throw new Exception(sprintf('%s%s%s', $message, PHP_EOL, json_encode($errorJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)));
    }

    protected function isDivisible(float $from, float $value) : bool
    {
        return ($from * 1000000000) % $value === 0;
    }

}
