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

    public function parts() : Collection
    {
        $data = collect();
        
        foreach ($this->crawler->filterXPath('//part') as $partDom) {
            $data->push(new Part($partDom));
        }

        return $data;
       
    }

}