<?php
namespace App\Services\Music;

use Symfony\Component\DomCrawler\Crawler as DOMCrawler;

abstract class Node
{
    protected DOMCrawler $crawler;

    protected NodeInterface $parentNode;

    public function __construct(DOMCrawler $crawler, NodeInterface $parentNode)
    {
        $this->crawler = $crawler;
        $this->parentNode = $parentNode;
    }

    public function crawler() : DOMCrawler
    {
        return $this->crawler;
    }

    public function parentCrawler() : ?DOMCrawler
    {
        return $this->parentNode->crawler();
    }

    public function __toString()
    {
        return $this->crawler->outerHtml();
    }
    
}