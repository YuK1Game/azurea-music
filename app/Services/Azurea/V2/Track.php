<?php
namespace App\Services\Azurea\V2;

use App\Services\Music\V2\MusicXML;
use App\Services\Music\V2\MusicXML\Parts\Measure;
use App\Services\Music\V2\MusicXML\Parts\Track as MusicXMLTrack;
use App\Services\Azurea\V2\Note as AzureaNote;
use Illuminate\Support\Collection;

class Track
{
    protected MusicXMLTrack $musicXmlTrack;

    protected int $currentTempo = 120;

    protected int $currentDivision = 12;

    protected int $currentBeat = 4;

    protected int $currentBeatType = 4;

    protected int $currentKey = 0;

    public function __construct(MusicXMLTrack $musicXmlTrack)
    {
        $this->musicXmlTrack = $musicXmlTrack;
    }

    public function measures()
    {
        $notes = collect();
        $prevNote = null;

        $this->musicXmlTrack->notes()->each(function($note) use(&$notes, &$prevNote) {
            $currentMeasure = $note->getMeasure();

            $modifyMeasures = collect([
                'modifyMeasureAttribute' => $this->modifyMeasureAttribute($currentMeasure),
                'modifyMeasureDirection' => $this->modifyMeasureDirection($currentMeasure),
            ]);

            $azureaNote = new AzureaNote($note);
            $azureaNote->setPrevAzureaNote($prevNote);
            $azureaNote->setCurrentTrackProperties($this->getCurrentTrackProperties());
            $notes->push($azureaNote);

            $prevNote = $azureaNote;
        });

        return $notes->groupBy(function(AzureaNote $azureaNote) {
            return $azureaNote->getCurrentMeasureNumber();
        });
    }

    protected function getCurrentTrackProperties() : Collection
    {
        return collect([
            'currentDivision' => $this->currentDivision,
            'currentBeat'     => $this->currentBeat,
            'currentBeatType' => $this->currentBeatType,
            'currentKey'      => $this->currentKey,
        ]);
    }
    
    protected function modifyMeasureAttribute(Measure $measure) : ?Collection
    {
        if ($attribute = $measure->attribute()) {
            return $this->modifyAttribute(['division', 'beat', 'beatType', 'key'], $attribute);
        }
        return null;
    }

    protected function modifyMeasureDirection(Measure $measure) : ?Collection
    {
        if ($direction = $measure->direction()) {
            return $this->modifyAttribute(['tempo'], $direction);
        }
        return null;
    }

    protected function modifyAttribute(array $keys, $data) : Collection
    {
        $keys = collect($keys);

        return $keys->mapWithKeys(function(string $key) use($data) {
            $dataName = sprintf('current%s', ucfirst($key));
            $value = $data->{ $key }();

            if ($value !== null && $value !== $this->{ $dataName }) {
                $this->{ $dataName } = $value;
                // echo sprintf('Change value [%s] => %s' . PHP_EOL, $key, $value);
                return [ $key => $value ];
            }
            return [ $key => null ];
        })
        ->filter(function($value) {
            return !! $value;
        });
    }

}