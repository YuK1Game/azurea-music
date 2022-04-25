<?php
namespace App\Services\MusicXML\MusicXML;

use App\Services\MusicXML\MusicXML\Note;
use App\Services\MusicXML\MusicXML\Backup;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use DOMElement;

class Measure
{
    protected $element;

    public function __construct(DOMElement $domElement)
    {
        $this->crawler = new DOMCrawler($domElement);
    }

    public function notes() : Collection
    {
        $data = collect();

        foreach ($this->crawler->filterXPath('//note|//backup') as $dom) {
            switch ($dom->nodeName) {
                case 'note' :
                    $data->push(new Note($dom)); 
                    break;
                case 'backup':
                    $data->push(new Backup($dom)); 
                    break;
            }
        }

        return $data;
    }
}