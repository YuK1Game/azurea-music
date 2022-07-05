<?php
namespace App\Services\Azurea\V2\Managers;

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

    public function getDuration() : ?int
    {
        if ($this->isBackup()) {
            return $this->getWholeDuration() - $this->measureChildren->duration();
        }
        return $this->measureChildren->duration();
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
            $this->throwDurationManagerException($e);

        } catch (DivisionByZeroError $e) {
            $this->throwDurationManagerException($e);
        }

    }

    protected function getBaseDurationAndDotCount() : Collection
    {
        $baseDuration = $this->getDuration();
        $durations = collect();

        foreach ($this->createDurationTable() as $durationTableRow) {
            $dot      = $durationTableRow['dot'];
            $duration = $durationTableRow['duration'];
            $value    = $durationTableRow['value'];

            if ($baseDuration >= $value) {
                $baseDuration -= $value;
                $durations->push([
                    'duration' => $duration,
                    'dot' => $dot,
                ]);
            }
        }

        if ($baseDuration <= 0) {
            return $durations;
        }

        $this->throwDurationManagerException('Duration is not acceptable for the table.');
    }

    public function createDurationTable() : Collection
    {
        $list = collect();
        $value = $this->getWholeDuration();

        for ($value = 1 ; $value <= $this->getWholeDuration() ; $value++) {
            $duration = $this->getWholeDuration() / $value;
            $this->isIntegerValue($duration) && $list->push($duration);
        }

        $list = $list->map(function($value) {
            return $this->createDotTable()->map(function(Collection $dotTableRow) use($value) {
                return collect([
                    'dot'      => $dotTableRow->get('dot'),
                    'duration' => $this->getWholeDuration() / $value,
                    'value'    => $value * $dotTableRow->get('ratio'),
                ]);
            });
        })
        ->flatten(1)
        ->filter(function(Collection $durationTableRow) {
            return $this->isIntegerValue($durationTableRow->get('value'))
                && $durationTableRow->get('value') <= $this->getWholeDuration();
        })
        ->sortByDesc('value');

        return $list;
    }

    protected function createDotTable() : Collection
    {
        return collect([
            collect(['dot' => 0, 'ratio' => 1.000 ]),
            collect(['dot' => 1, 'ratio' => 1.500 ]),
            collect(['dot' => 2, 'ratio' => 1.750 ]),
            // collect(['dot' => 3, 'ratio' => 1.875 ]),
        ]);
    }

    protected function throwDurationManagerException(string $message) : void
    {
        $errorJson = [
            'message' => $message,
            'measure_number' => $this->azureaNote->getCurrentMeasureNumber(),
            'durations' => [
                'whole_duration' => $this->getWholeDuration(),
                'duration' => $this->getDuration(),
                'duration_table' => $this->createDurationTable(),
            ],
            'note' => [
                'class' => get_class($this->measureChildren),
                'xml' => $this->measureChildren->getXml(),
            ],
        ];

        throw new Exception(sprintf('%s%s%s', 'Error', PHP_EOL, json_encode($errorJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)));
    }

    protected function isIntegerValue(float $value) : bool
    {
        return $value === floor($value);
    }

}
