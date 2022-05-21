<?php
namespace App\Services\Azurea;

use App\Services\Music\Part as MusicPart;

use Illuminate\Support\Collection;

use App\Services\Music\{
    Parts\Measure as MusicMeasure,
    Parts\Measures\Note as MusicNote,
};

use App\Services\Azurea\Note;
use App\Services\Music\Parts\Measures\MeasureKey;

class Part
{
    protected MusicPart $part;

    protected int $maxDuration;

    protected ?Collection $measureDurations;

    protected ?Note $prevNote = null;

    protected MusicMeasure $metaMeasure;

    protected ?MeasureKey $measureKey = null;

    protected int $tempo = 120;

    public function __construct(MusicPart $part)
    {
        $this->part = $part;

        $this->initMeasureDurations();
        $this->initMaxDuration();
        $this->initMetaMeasure();
    }

    private function initMeasureDurations() : void
    {
        $this->measureDurations = $this->part->trackA()->map(function(MusicMeasure $measure) {
            return $measure->totalDuration();
        });
    }

    private function initMaxDuration() : void
    {
        $this->maxDuration = $this->part->maxDuration();
    }

    private function initMetaMeasure() : void
    {
        $this->metaMeasure = $this->part->trackA()->first();
    }

    protected function setMeasureDurationByIndex(int $index, ?int $measureDuration) : void
    {
        if ( ! $this->measureDurations->has($index) && $measureDuration) {
            $this->measureDurations->put($index, $measureDuration);
        }
    }

    protected function getMeasureDurationByIndex(int $index) : int
    {
        return $this->measureDurations->get($index);
    }

    public function exportCode() : void
    {
        $this->part->tracks()->each(function(Collection $track, int $trackIndex) {
            echo sprintf('TrackNumber [%d]' . PHP_EOL . PHP_EOL, $trackIndex + 1);

            $track->each(function(?MusicMeasure $measure, int $measureIndex) {

                if (($tempo = $measure->tempo()) && $tempo !== $this->tempo) {
                    echo sprintf('t%d', $tempo);
                    $this->tempo = $tempo;
                }

                if ($measureKey = $measure->measureKey()) {
                    $this->measureKey = $measureKey;
                    // echo "\n" . $measureKey . "\n";
                }

                $this->exportCodeByMeasure($measure, $this->measureDurations->get($measureIndex));
                echo PHP_EOL;
            });

            echo PHP_EOL;

        });
    }

    public function exportCodeByMeasure(?MusicMeasure $measure, int $measureDuration) : void
    {
        $azureaNotes = $this->getNotesByMeasure($measure) ?? $this->getBlankNotesWithMeasureDuration($measureDuration);

        $azureaNotes->each(function(Note $note) {
            $note->setPrevNote($note);
            echo $note->code();
            $this->prevNote = $note;
        });
    }

    protected function getNotesByMeasure(?MusicMeasure $measure) : ?Collection
    {
        return $measure->hasNotes() ? $measure->notes()->map(function(MusicNote $note) {
            $azureaNote = new Note($note, $this->maxDuration);
            $azureaNote->setMeasureKey($this->measureKey);
            return $azureaNote;
        }) : null;
    }

    protected function getBlankNotesWithMeasureDuration($measureDuration) : Collection
    {
        $azureaNote = new Note(null, $this->maxDuration);
        $azureaNote->setNoteDuration($measureDuration);
        return collect([ $azureaNote ]);
    }

}