<?php
namespace App\Services\MusicXML\MusicXML;

use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use DOMElement;
use App\Services\MusicXML\MusicXML\Measures\{ MeasureContent, MeasureContentInterface };

class Note extends MeasureContent implements MeasureContentInterface
{
    protected $crawler;

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

    public function isRest() : bool
    {
        return $this->crawler->filterXPath('//rest')->count() > 0;
    }

    public function pitchStep() : string
    {
        return $this->getTextByFilterPath('//pitch/step');
    }

    public function pitchOctave() : int
    {
        return (int) $this->getTextByFilterPath('//pitch/octave');
    }

    public function duration() : int
    {
        return (int) $this->getTextByFilterPath('//duration');
    }

    protected function pitchStepToText(string $pitchStep) : string
    {
        switch (strtolower($pitchStep)) {
            case 'a' : return 'ラ';
            case 'b' : return 'シ';
            case 'c' : return 'ド';
            case 'd' : return 'レ';
            case 'e' : return 'ミ';
            case 'f' : return 'ファ';
            case 'g' : return 'ソ';
        }
    }

    public function toAzureaCode() : string
    {
        $duration = $this->duration();

        if ($this->isRest()) {
            return sprintf('%s%d', 'Rest', $duration);
        } else {
            return sprintf('%s%d', $this->pitchStepToText($this->pitchStep()), $duration);
        }
    }

}