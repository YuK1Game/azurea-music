<?php
namespace App\Services\Azurea\V2;

use App\Services\Music\V2\MusicXML;
use App\Services\Music\V2\MusicXML\Parts\Measure;
use App\Services\Music\V2\MusicXML\Parts\Measures\Attribute;
use App\Services\Music\V2\MusicXML\Parts\Measures\Note;
use Illuminate\Support\Collection;

use App\Services\Azurea\V2\Track as AzureaTrack;
use App\Services\Azurea\V2\Note as AzureaNote;
use App\Services\Music\V2\MusicXML\Part as MusicXMLPart;
use App\Services\Music\V2\MusicXML\Parts\Track as MusicXMLTrack;

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
        return $this->musicXml->parts()->map(function(MusicXMLPart $part) {

            $tracks = $part->tracks()->map(function(MusicXMLTrack $track) {
                
                $azureaTrack = new AzureaTrack($track);
                
                $measureNotes = $azureaTrack->measures()->mapWithKeys(function(Collection $notes, int $measureId) {
                    
                    $noteCode = collect();

                    if ($tempo = $this->getTempoByMeasureId($measureId)) {
                        $noteCode->push(sprintf('t%d', $tempo));
                    }

                    $notes->each(function(AzureaNote $azureaNote) use($noteCode) {
                        $noteCode->push($azureaNote->getCode());
                    });

                    return [ $measureId => $noteCode ];
                });

                return collect([
                    'measures' => $measureNotes,
                ]);
            });

            return collect([
                'id' => $part->id(),
                'part_name' => $part->scorePartName(),
                'tracks' => $tracks,
            ]);
        });
    }

}