<?php
namespace App\Services\Music\V2\MusicXML\Parts;

use App\Services\Music\V2\MusicXMLChildrenInterface;
use App\Services\Music\V2\MusicXML\Part;
use App\Services\Music\V2\MusicXML\Parts\MeasureTrack;
use App\Services\Music\V2\MusicXML\Parts\Measures\{
    Note,
    BlankNote,
    Backup,
    Attribute,
    Direction,
    Forward,
};

use Illuminate\Support\Collection;
use SimpleXMLElement;


class Measure implements MusicXMLChildrenInterface
{
    protected SimpleXMLElement $xml;

    protected Part $parent;

    public function __construct(SimpleXMLElement $xml, $parent)
    {
        $this->xml = $xml;
        $this->parent = $parent;
    }

    public function number() : int
    {
        if (isset($this->xml['number'])) {
            return (int) $this->xml['number'];
        }
        return null;
    }

    public function attribute() : ?Attribute
    {
        if ($attribute = $this->xml->attributes) {
            return new Attribute($attribute, $this);
        }
        return null;
    }

    public function direction() : ?Direction
    {
        if ($direction = $this->xml->direction) {
            return new Direction($direction, $this);
        }
        return null;
    }

    public function notes() : Collection
    {
        $data = collect();
        $noteIndex = 1;

        foreach ($this->xml->xpath('note|backup|forward|direction') as $node) {
            if ($node) {
                switch ($node->getName()) {
                    case 'note':
                        $data->push(new Note($node, $this, $noteIndex++));
                        break;
                    case 'forward':
                        $data->push(new Forward($node, $this));
                        break;
                    case 'backup':
                        $data->push(new Backup($node, $this));
                        break;
                    case 'direction':
                        $data->push(new Direction($node, $this));
                        break;
                }
            }
        }

        return $data;
    }

    public function getDividedTrackByIndex(int $index) : Collection
    {
        if ($track = $this->getDividedTracks()->get($index)) {
            return $track;
        }
        return MeasureTrack::create(collect([ new BlankNote($this) ]), $this);
    }

    public function getDividedTracks()
    {
        $dividedTracks = $this->notes()->chunkWhile(function($anyNote) {
            return ! $anyNote instanceof Backup;
        })
        ->map(function(Collection $notes) {
            return MeasureTrack::create($notes, $this);
        });

        return $dividedTracks;
    }

    public function getPart() : Part
    {
        return $this->parent;
    }

}
