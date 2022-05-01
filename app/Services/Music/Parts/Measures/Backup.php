<?php
namespace App\Services\Music\Parts\Measures;

use App\Services\Music\{ Node, NodeInterface };
use App\Services\Music\Parts\Measures\MeasureChildrenInterface;

use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use Illuminate\Support\Collection;

class Backup extends Node implements NodeInterface, MeasureChildrenInterface
{
    public function duration() : int
    {
        return (int) $this->crawler->filter('duration')->innerText();
    }
}