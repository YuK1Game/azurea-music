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

    public function id() : ?string
    {
        return $this->xml['id'] ?? null;
    }

    public function scorePart() : ?SimpleXMLElement
    {
        $scorePartElement = $this->parent->scoreParts();

        foreach ($scorePartElement->{'score-part'} as $scorePart) {
            if (isset($scorePart['id'])) {
                if ((string) $scorePart['id'] === $this->id()) {
                    return $scorePart;
                }
            }
        }
        throw new \Exception(sprintf('Not found part. [%s]', $this->id()));
    }

    public function scorePartName() : string
    {
        return $this->scorePart()->{'part-name'};
    }

    public function measures() : Collection
    {
        $data = collect();
        $index = 0;

        foreach ($this->xml->measure as $measureXml) {
            if ($measureXml) {
                $measure = new Measure($measureXml, $this);
                $measure->setIndex($index++);
                $data->push($measure);
            }
        }
        
        return $data;
    }

    public function tracks() : Collection
    {
        $maxTrackCount = $this->maxTrackCount();

        $tracks = collect();

        for($i = 0 ; $i < $maxTrackCount ; $i++) {
            $measureTracks = $this->measures()->map(function(Measure $measure) use($i) {
                return $measure->getDividedTrackByIndex($i);
            });
            $tracks->push(new Track($measureTracks, $this));
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