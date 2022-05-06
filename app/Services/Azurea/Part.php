<?php
namespace App\Services\Azurea;

use App\Services\Music\Part as MusicPart;

use Illuminate\Support\Collection;

use App\Services\Music\{
    Parts\MeasureChunk as MusicMeasureChunk,
    Parts\Measures\Note as MusicNote,
};

use App\Services\Azurea\Note;

class Part
{
    protected MusicPart $part;

    protected int $maxDuration;

    protected Collection $properties;

    protected Collection $measureDurations;

    public function __construct(MusicPart $part)
    {
        $this->part = $part;
        $this->maxDuration = $part->maxDuration();
        $this->properties = collect();
        $this->measureDurations = collect();
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

            $track->each(function(?MusicMeasureChunk $measureChunk, int $measureIndex) {
                $this->setMeasureDurationByIndex($measureIndex, $measureChunk ? $measureChunk->totalNoteDuration() : null);
                $this->exportCodeByMeasureChunk($measureChunk, $this->getMeasureDurationByIndex($measureIndex));
                echo PHP_EOL;
            });

            echo PHP_EOL;

        });
    }

    public function exportCodeByMeasureChunk(?MusicMeasureChunk $measureChunk, int $measureDuration) : void
    {

        if ($measureChunk) {
            $measureChunk->notes()->each(function(MusicNote $note) {
                $azureaNote = new Note($note, $this->maxDuration);
                echo $azureaNote->code();
            });
        } else {
            $azureaNote = new Note(null, $this->maxDuration);
            $azureaNote->setNoteDuration($measureDuration);
            echo $azureaNote->code();
        }
    }

}