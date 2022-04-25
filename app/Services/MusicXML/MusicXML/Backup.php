<?php
namespace App\Services\MusicXML\MusicXML;

use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use DOMElement;

class Backup
{
    protected $element;

    public function __construct(DOMElement $domElement)
    {
        $this->crawler = new DOMCrawler($domElement);
    }
}