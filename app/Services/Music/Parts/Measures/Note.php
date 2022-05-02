<?php
namespace App\Services\Music\Parts\Measures;

use App\Services\Music\{ Node, NodeInterface };
use App\Services\Music\Parts\Measures\MeasureChildrenInterface;

use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use Illuminate\Support\Collection;

class Note extends Node implements NodeInterface, MeasureChildrenInterface
{
    protected ?int $totalNoteDuration;
    
    public function setTotalNoteDuration(int $totalNoteDuration) : void
    {
        $this->totalNoteDuration = $totalNoteDuration;
    }

    public function getTotalNoteDuration() : ?int
    {
        return $this->totalNoteDuration;
    }

    public function duration() : int
    {
        return (int) $this->crawler->filter('duration')->innerText();
    }

    public function isRest() : bool
    {
        return $this->crawler->filter('rest')->count() > 0;
    }

    public function isChord() : bool
    {
        return $this->crawler->filter('chord')->count() > 0;
    }

    public function pitchStep() : string
    {
        $step = $this->crawler->filter('pitch > step')->innerText() ?? null;
        return $step ? strtolower($step) : null;
    }

    public function pitchOctave() : int
    {
        return (int) $this->crawler->filter('pitch > octave')->innerText() ?? 0;
    }
}