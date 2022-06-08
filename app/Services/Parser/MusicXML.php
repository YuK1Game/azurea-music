<?php
namespace App\Services\Parser;

use App\Services\Parser\Parser;
use App\Services\Parser\MusicXML\Part;
use Illuminate\Support\Collection;

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

}