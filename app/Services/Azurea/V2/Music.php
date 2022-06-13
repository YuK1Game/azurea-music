<?php
namespace App\Services\Azurea\V2;

use App\Services\Music\V2\MusicXML;
use App\Services\Music\V2\MusicXML\Parts\Measure;
use App\Services\Music\V2\MusicXML\Parts\Measures\Attribute;
use App\Services\Music\V2\MusicXML\Parts\Measures\Note;
use Illuminate\Support\Collection;

use App\Services\Azurea\V2\Track as AzureaTrack;
use App\Services\Azurea\V2\Note as AzureaNote;


class Music
{
    protected MusicXML $musicXml;

    public function __construct(string $filename)
    {
        $this->musicXml = new MusicXML($filename);
    }

    protected function getTempoByMeasureId(int $measureId) : ?int
    {
        $measures = $this->musicXml->parts()->first()->measures();
        $measure = $measures->filter(function(Measure $measure) use($measureId) {
            return $measure->number() === $measureId;
        })->first();
        
        if ($direction = $measure->direction()) {
            return $direction->tempo();
        }

        return null;
    }

    public function getCodes()
    {
        $codes = collect();

        foreach ($this->musicXml->parts() as $part) {
            foreach ($part->tracks() as $track) {
                $azureaTrack = new AzureaTrack($track);
                $measures = $azureaTrack->measures();
                $measures->each(function(Collection $notes, int $measureId) use($codes) {
                    
                    if ($tempo = $this->getTempoByMeasureId($measureId)) {
                        echo sprintf('t%d' . PHP_EOL, $tempo);
                    }

                    $notes->each(function(AzureaNote $azureaNote) {
                        echo $azureaNote->getCode();
                    });

                    echo PHP_EOL;
                });

                echo PHP_EOL;
                echo PHP_EOL;
            }
        }

        return $codes;
    }

}