<?php
namespace App\Services\MusicXML\MusicXML;

use App\Services\MusicXML\MusicXML\Measure;
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
}