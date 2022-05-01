<?php
namespace App\Services\Music;

use Symfony\Component\DomCrawler\Crawler as DOMCrawler;

interface NodeInterface
{
    public function __construct(DOMCrawler $crawler, NodeInterface $parentNode);

    public function crawler() : DOMCrawler;
}