<?php
namespace App\Services\Parser\MusicXML;

use App\Services\Parser\MusicXMLChildrenInterface;
use App\Services\Parser\MusicXML;
use App\Services\Parser\MusicXML\Parts\Measure;

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

    public function getMusicXml() : MusicXML
    {
        return $this->parent;
    }

}