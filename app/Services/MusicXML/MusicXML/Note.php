<?php
namespace App\Services\MusicXML\MusicXML;

use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use DOMElement;

class Note
{
    protected $element;

    public function __construct(DOMElement $domElement)
    {
        $this->crawler = new DOMCrawler($domElement);
    }

    public function pitchStep() : string
    {
        return $this->crawler->filterXPath('//pitch/step')->text();
    }

    public function pitchOctave() : int
    {
        return (int) $this->crawler->filterXPath('//pitch/octave')->text();
    }

    public function duration() : int
    {
        return (int) $this->crawler->filterXPath('//duration')->text();
    }
}