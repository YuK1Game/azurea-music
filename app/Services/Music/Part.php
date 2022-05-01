<?php
namespace App\Services\Music;

use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use Illuminate\Support\Collection;

class Part extends Node implements NodeInterface
{
    public function measures() : Collection
    {
        $parts = collect();

        $this->crawler->filter('measure')->each(function(DOMCrawler $crawler) use($parts) {
            $parts->push(new Parts\Measure($crawler, $this));
        });

        return $parts;
    }

}