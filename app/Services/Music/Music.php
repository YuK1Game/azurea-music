<?php
namespace App\Services\Music;

use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use Illuminate\Support\Collection;

class Music extends Node implements NodeInterface
{
    protected DOMCrawler $crawler;

    public function __construct(DOMCrawler $crawler, ?NodeInterface $parentNode = null)
    {
        $this->crawler = $crawler;
    }

    public function scoreParts() : Collection
    {
        $scoreParts = collect();

        $this->crawler->filter('score-part')->each(function(DOMCrawler $crawler) use($scoreParts) {
            $scoreParts->push(new ScorePart($crawler, $this));
        });

        return $scoreParts;
    }

}