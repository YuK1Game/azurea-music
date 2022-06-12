<?php
namespace App\Services\Azurea;

use App\Services\Music\Part as MusicPart;

use Illuminate\Support\Collection;

use App\Services\Music\{
    Parts\Measure as MusicMeasure,
    Parts\Measures\Note as MusicNote,
};

use App\Services\Azurea\{ Measure, Note };
use App\Services\Music\Parts\Measures\MeasureKey;

class Part
{
    protected MusicPart $part;

    protected int $maxDuration;

    protected ?Collection $measureLengths;

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
        $this->measureLengths = $this->part->trackA()->map(function(MusicMeasure $measure) {
            return $measure->totalLength();
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

    protected function getMeasureLengthByIndex(int $index) : int
    {
        return $this->measureLengths->get($index);
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
                $azureaMeasure = new Measure($measure);

                if (($tempo = $azureaMeasure->tempo()) && $tempo !== $this->tempo) {
                    $this->tempos->put($measureIndex, $tempo);
                }

                if ($tempo = $this->tempos->get($measureIndex)) {
                    echo sprintf('t%d', $tempo);
                    echo PHP_EOL;
                }

                if ($measureKey = $azureaMeasure->measureKey()) {
                    $this->measureKey = $measureKey;
                }

                $azureaMeasure->setMeasureKey($this->measureKey);

                echo '[' . $azureaMeasure->getNumber() . '] ';

                $this->exportCodeByMeasure($azureaMeasure, $this->getMeasureLengthByIndex($measureIndex));
                echo PHP_EOL;
            });

            echo PHP_EOL;

        });
    }

    public function exportCodeByMeasure(?Measure $measure, int $globalMeasureTotalLength) : void
    {
        $pitchSharps = collect();
        $pitchFlats = collect();
        $pitchNaturals = collect();

        $measure->getNotes()->each(function(Note $note) use(&$pitchSharps, &$pitchFlats, &$pitchNaturals) {

            $note->isSharp() && $pitchSharps->push($note->defaultPitch());
            $note->isFlat() && $pitchFlats->push($note->defaultPitch());
            $note->isNatural() && $pitchNaturals->push($note->defaultPitch());

            $note->setPrevNote($note);
            $note->setMeasureSharpPitches($pitchSharps);
            $note->setMeasureFlatPitches($pitchFlats);
            $note->setMeasureNaturalPitches($pitchNaturals);

            // echo $note->debugDuration();
            echo $note->code();

            $this->prevNote = $note;
            
        });

        if ($measure->hasNotes()) {
            if ($measure->totalLength() < $globalMeasureTotalLength) {
                echo sprintf('r%d', $globalMeasureTotalLength / $measure->totalLength());
            }
        }

    }

}