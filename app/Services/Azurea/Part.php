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

    protected ?Collection $tempos;

    public function __construct(MusicPart $part)
    {
        $this->part = $part;

        $this->initMeasureDurations();
        $this->initMaxDuration();
        $this->initMetaMeasure();
        $this->initTempos();
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

    private function initTempos() : void
    {
        $this->tempos = collect();

        $this->part->tracks()->each(function(Collection $track) {
            $track->each(function(?MusicMeasure $measure, int $measureIndex) {

                if (($tempo = $measure->tempo()) && $tempo !== $this->tempo) {
                    $this->tempos->put($measureIndex, $tempo);
                }
            });
        });
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

    public function setTempos(Collection $tempos) : void
    {
        $this->tempos = $tempos;
    }

    public function getTempos() : Collection
    {
        ! $this->tempos && $this->initTempos();
        return $this->tempos;
    }

    public function exportCode() : void
    {
        $this->part->tracks()->each(function(Collection $track, int $trackIndex) {
            echo sprintf('TrackNumber [%d]' . PHP_EOL . PHP_EOL, $trackIndex + 1);

            $track->each(function(?MusicMeasure $measure, int $measureIndex) {

                if (($tempo = $measure->tempo()) && $tempo !== $this->tempo) {
                    $this->tempos->put($measureIndex, $tempo);
                }

                if ($tempo = $this->tempos->get($measureIndex)) {
                    echo sprintf('t%d', $tempo);
                }

                if ($measureKey = $measure->measureKey()) {
                    $this->measureKey = $measureKey;
                }

                $this->exportCodeByMeasure($measure, $this->measureDurations->get($measureIndex));
                echo PHP_EOL;
            });

            echo PHP_EOL;

        });
    }

    public function exportCodeByMeasure(?MusicMeasure $measure, int $measureDuration) : void
    {
        $pitchSharps = collect();
        $pitchFlats = collect();
        $pitchNaturals = collect();

        $azureaNotes = $this->getNotesByMeasure($measure) ?? $this->getBlankNotesWithMeasureDuration($measureDuration);

        $azureaNotes->each(function(Note $note) use(&$pitchSharps, &$pitchFlats, &$pitchNaturals) {

            $note->isSharp() && $pitchSharps->push($note->defaultPitch());
            $note->isFlat() && $pitchFlats->push($note->defaultPitch());
            $note->isNatural() && $pitchNaturals->push($note->defaultPitch());

            $note->setPrevNote($note);
            $note->setMeasureSharpPitches($pitchSharps);
            $note->setMeasureFlatPitches($pitchFlats);
            $note->setMeasureMaturalPitches($pitchNaturals);

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