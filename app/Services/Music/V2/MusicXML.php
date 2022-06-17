<?php
namespace App\Services\Music\V2;

use App\Services\Music\V2\Parser;
use App\Services\Music\V2\MusicXML\Part;
use Illuminate\Support\Collection;
use SimpleXMLElement;

class MusicXML
{
    protected $xml;

    public function __construct(string $filename)
    {
        $parser = new Parser($filename);
        $this->xml = $parser->getXml();
    }

    public function parts() : Collection
    {
        $data = collect();

        foreach ($this->xml->part as $part) {
            $part && $data->push(new Part($part, $this));
        }
        
        return $data;
    }

    public function scoreParts() : SimpleXMLElement
    {
        return $this->xml->{'part-list'};
    }

}