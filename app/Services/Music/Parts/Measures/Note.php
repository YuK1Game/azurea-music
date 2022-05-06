<?php
namespace App\Services\Music\Parts\Measures;

use App\Services\Music\{ Node, NodeInterface };
use App\Services\Music\Parts\Measures\MeasureChildrenInterface;

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
        $node = $this->crawler->filter('duration');
        return $node->count() > 0 ? (int) $node->innerText() : 0;
    }

    public function volume() : int
    {
        $node = $this->crawler->filter('volume');
        return $node->count() > 0 ? (int) $node->innerText() : 0;
    }

    public function isRest() : bool
    {
        return $this->crawler->filter('rest')->count() > 0;
    }

    public function isChord() : bool
    {
        return $this->crawler->filter('chord')->count() > 0;
    }

    public function accidental() : ?string
    {
        $node = $this->crawler->filter('accidental');
        return $node->count() > 0 ? $node->text() : null;
    }

    public function isFlat() : bool
    {
        return $this->accidental() === 'flat';
    }

    public function isSharp() : bool
    {
        return $this->accidental() === 'sharp';
    }

    public function isNatural() : bool
    {
        return $this->accidental() === 'natural';
    }

    public function isTieStart() : bool
    {
        return $this->crawler->filter('notations > tied[type="start"]')->count() > 0;
    }

    public function isTieEnd() : bool
    {
        return $this->crawler->filter('notations > tied[type="stop"]')->count() > 0;
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