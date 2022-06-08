<?php
namespace App\Services\Parser\MusicXML\Parts;

use App\Services\Parser\MusicXMLChildrenInterface;
use App\Services\Parser\MusicXML\Part;
use App\Services\Parser\MusicXML\Parts\Measures\{
    Note,
    Backup,
    Attribute,
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

    public function getPart() : Part
    {
        return $this->parent;
    }

}