<?php
namespace App\Services\Azurea\V2;

use App\Services\Music\V2\MusicXML;
use App\Services\Music\V2\MusicXML\Parts\Measure;
use App\Services\Music\V2\MusicXML\Parts\Measures\Attribute;
use App\Services\Music\V2\MusicXML\Parts\Measures\Note;
use Illuminate\Support\Collection;

class Music
{
    protected MusicXML $musicXml;

    protected int $currentTempo = 120;

    protected int $currentDivision = 12;

    protected int $currentBeat = 4;

    protected int $currentBeatType = 4;

    public function __construct(string $filename)
    {
        $this->musicXml = new MusicXML($filename);
    }

    public function getCodes()
    {
        $codes = collect();

        foreach ($this->musicXml->parts() as $part) {
            foreach ($part->tracks() as $track) {
                foreach ($track->notes() as $note) {
                    $currentMeasure = $note->getMeasure();
                    $modifyAttributes = $this->modifyMeasureAttribute($currentMeasure);
                    $modifyDirections = $this->modifyMeasureDirection($currentMeasure);

                    if ($note instanceof Note) {
                        echo PHP_EOL;
                    }
                }
            }
        }

        return $codes;
    }

    protected function modifyMeasureAttribute(Measure $measure) : ?Collection
    {
        if ($attribute = $measure->attribute()) {
            return $this->modifyAttribute(['division', 'beat', 'beatType'], $attribute);
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

            if ($value && $value !== $this->{ $dataName }) {
                $this->{ $dataName } = $value;
                echo sprintf('Change value [%s] => %s' . PHP_EOL, $key, $value);
                return [ $key => $value ];
            }
            return [ $key => null ];
        })
        ->filter(function($value) {
            return !! $value;
        });
    }

}