<?php
namespace App\Services\MusicXML\MusicXML;

use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use DOMElement;
use App\Services\MusicXML\MusicXML\Measures\{ MeasureContent, MeasureContentInterface };

class Note extends MeasureContent implements MeasureContentInterface
{
    protected $crawler;

    protected ?string $pitchStep;

    protected ?int $pitchOctave;

    protected ?int $duration;

    protected bool $isFlat;

    protected bool $isSharp;

    protected bool $isTimeModification;

    protected ?int $actualNote;

    protected ?int $normalNote;

    protected array $steps = ['c', 'c+', 'd', 'd+', 'e', 'e+', 'f', 'f+', 'g', 'g+', 'a', 'a+', 'b'];

    public function __construct(DOMElement $domElement)
    {
        $this->crawler = new DOMCrawler($domElement);

        $this->pitchStep          = strtolower($this->getTextByFilterPath('//pitch/step'));
        $this->pitchOctave        = (int) $this->getTextByFilterPath('//pitch/octave');
        $this->duration           = (int) $this->getTextByFilterPath('//duration');
        $this->isFlat             = $this->getTextByFilterPath('//accidental') === 'flat';
        $this->isSharp            = $this->getTextByFilterPath('//accidental') === 'sharp';
        $this->isTimeModification = $this->hasDomByFilterPath('//time-modification');
        $this->actualNote         = (int) $this->getTextByFilterPath('//time-modification/actual-notes');
        $this->normalNote         = (int) $this->getTextByFilterPath('//time-modification/normal-notes');
    }

    public function getCode() : string
    {
        return sprintf('%s%d', $this->pitchStep(), $this->pitchOctave());
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

    public function isFlat() : bool
    {
        return $this->isFlat;
    }

    public function isSharp() : bool
    {
        return $this->isSharp;
    }

    public function setFlat(?bool $isFlat = true) : void
    {
        $this->isFlat = $isFlat;
    }

    public function setSharp(?bool $isSharp = true) : void
    {
        $this->isSharp = $isSharp;
    }

    public function pitchStep() : string
    {
        return $this->isRest() ? 'r' : $this->pitchStep;
    }

    public function pitchOctave() : int
    {
        return $this->pitchOctave;
    }

    public function duration() : int
    {
        // if ($this->isTimeModification) {
        //     return $this->duration * $this->normalNote / $this->actualNote;
        // }
        return $this->duration;
    }

    public function addLevel() : Note
    {
        $index = collect($this->steps)->search($this->pitchStep);
        $addIndex = $index + 1;

        if (isset($this->steps[$addIndex])) {
            $this->pitchStep = $this->steps[$addIndex];
        } else {
            $this->pitchStep = $this->steps[0];
            $this->pitchOctave += 1;
        }
        
        return $this;
    }

    public function subLevel() : Note
    {
        $index = collect($this->steps)->search($this->pitchStep);
        $subIndex = $index - 1;

        if (isset($this->steps[$subIndex])) {
            $this->pitchStep = $this->steps[$subIndex];
        } else {
            $this->pitchStep = $this->steps[12];
            $this->pitchOctave -= 1;
        }

        return $this;
    }

    public function toXml() : string
    {
        return $this->crawler->outerHtml();
    }

}