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
        return $this->hasDomByFilterPath('//rest');
    }

    public function isChord() : bool
    {
        return $this->hasDomByFilterPath('//chord');
    }

    public function pitchStep() : string
    {
        if ($this->isRest()) {
            return 'r';
        } else {
            return $this->getTextByFilterPath('//pitch/step');
        }
    }

    public function pitchOctave() : int
    {
        return (int) $this->getTextByFilterPath('//pitch/octave');
    }

    public function duration() : int
    {
        return (int) $this->getTextByFilterPath('//duration');
    }

    public function toAzureaCode() : string
    {
        $duration = $this->duration();

        if ($this->isRest()) {
            return sprintf('%s%s', 'Rest', $duration);
        } else {
            return sprintf('%s%s', $this->pitchStep(), $duration);
        }
    }

}