<?php
namespace App\Services\MusicXML\MusicXML;

use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use DOMElement;
use App\Services\MusicXML\MusicXML\MeasureContentInterface;

class Backup implements MeasureContentInterface
{
    protected $element;

    public function __construct(DOMElement $domElement)
    {
        $this->crawler = new DOMCrawler($domElement);
    }
    
    public function isNote(): bool
    {
        return false;
    }

    public function isBackup(): bool
    {
        return true;
    }

    public function duration() : int
    {
        return (int) $this->crawler->filterXPath('//duration')->text();
    }
}