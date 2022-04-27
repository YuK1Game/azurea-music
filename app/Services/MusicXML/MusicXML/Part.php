<?php
namespace App\Services\MusicXML\MusicXML;

use Illuminate\Support\Collection;
use App\Services\MusicXML\MusicXML\Track;
use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use DOMElement;

class Part
{
    protected Collection $notesList;

    public function __construct(DOMElement $dom)
    {
        $this->crawler = new DOMCrawler($dom);
    }

    public function measures() : Collection
    {
        $data = collect();

        foreach ($this->crawler->filterXPath('//measure') as $dom) {
            $data->push(new Measure($dom));
        }

        return $data;
    }

    public function trackA()
    {
        $track = $this->measures()->map(function(Measure $measure) {
            return $measure->firstPartNotes();
        });
        return new Track($track);
    }

    public function trackB()
    {
        $track = $this->measures()->map(function(Measure $measure) {
            return $measure->secondPartNotes();
        });
        return new Track($track);
    }

}