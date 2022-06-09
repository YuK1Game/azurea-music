<?php
namespace App\Services\Music\V2\MusicXML;

use App\Services\Music\V2\MusicXMLChildrenInterface;
use App\Services\Music\V2\MusicXML;
use App\Services\Music\V2\MusicXML\Parts\Measure;
use App\Services\Music\V2\MusicXML\Parts\Track;
use App\Services\Music\V2\MusicXML\Parts\Measures\BlankNote;
use Illuminate\Support\Collection;
use SimpleXMLElement;

class Part implements MusicXMLChildrenInterface 
{
    protected SimpleXMLElement $xml;

    protected MusicXML $parent;

    public function __construct(SimpleXMLElement $xml, $parent)
    {
        $this->xml = $xml;
        $this->parent = $parent;
    }

    public function measures() : Collection
    {
        $data = collect();

        foreach ($this->xml->measure as $measure) {
            $measure && $data->push(new Measure($measure, $this));
        }
        
        return $data;
    }

    public function tracks() : Collection
    {
        $maxTrackCount = $this->maxTrackCount();

        $tracks = collect();

        for($i = 0 ; $i < $maxTrackCount ; $i++) {
            $track = $this->measures()->map(function(Measure $measure) use($i) {
                $dividedTracks = $measure->getDividedTracks();
                return $dividedTracks->get($i) ?? new BlankNote($measure);
            });
            $tracks->push(new Track($track->flatten(), $this));
        }

        return $tracks;
    }

    public function maxTrackCount() : int
    {
        return $this->measures()->max(function(Measure $measure) {
            $tracks = $measure->getDividedTracks();
            return $tracks->count();
        });
    }

    public function getMusicXml() : MusicXML
    {
        return $this->parent;
    }

}