<?php
namespace App\Services\MusicXML\MusicXML;

use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use DOMElement;
use App\Services\MusicXML\MusicXML\MeasureContentInterface;

class Note implements MeasureContentInterface
{
    protected $element;

    public function __construct(DOMElement $domElement)
    {
        $this->crawler = new DOMCrawler($domElement);
    }

    public function isNote(): bool
    {
        return true;
    }

    public function isBackup(): bool
    {
        return false;
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

    public function toAzureaCode() : string
    {
        $duration = 8 / $this->duration();
        return sprintf('%s%d', strtolower($this->pitchStep()), $duration);
    }

}