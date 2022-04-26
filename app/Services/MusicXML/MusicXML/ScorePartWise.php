<?php
namespace App\Services\MusicXML\MusicXML;

use App\Services\MusicXML\MusicXML\Measure;
use App\Services\MusicXML\MusicXML\Track;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler as DOMCrawler;

class ScorePartWise
{
    protected $crawler;

    public function __construct(DOMCrawler $crawler)
    {
        $this->crawler = $crawler;
    }

    public function measures() : Collection
    {
        $data = collect();

        foreach ($this->crawler->filterXPath('//part/measure') as $dom) {
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