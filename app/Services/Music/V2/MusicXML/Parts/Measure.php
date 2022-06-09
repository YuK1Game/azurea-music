<?php
namespace App\Services\Music\V2\MusicXML\Parts;

use App\Services\Music\V2\MusicXMLChildrenInterface;
use App\Services\Music\V2\MusicXML\Part;
use App\Services\Music\V2\MusicXML\Parts\Measures\{
    Note,
    Backup,
    Attribute,
    Direction,
    Track,
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

        foreach ($this->xml->xpath('note|backup') as $node) {
            if ($node) {
                switch ($node->getName()) {
                    case 'note':
                        $data->push(new Note($node, $this));
                        break;
                    case 'backup':
                        $data->push(new Backup($node, $this));
                        break;
                }
            }
        }
        
        return $data;
    }

    public function getDividedTracks()
    {
        return $this->notes()->chunkWhile(function($anyNote) {
            return $anyNote instanceof Note;
        });
    }

    public function getPart() : Part
    {
        return $this->parent;
    }

}