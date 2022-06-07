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

    public function duration() : ?int
    {
        $node = $this->crawler->filter('type');
        
        if ($node->count() > 0) {
            switch ($node->innerText()) {
                case 'whole'   : return  1;
                case 'half'    : return  2;
                case 'quarter' : return  4;
                case 'eighth'  : return  8;
                case '16th'    : return 16;
                default : throw new \Exception('Error');
            }
        }

        return 1;
    }

    public function length() : int
    {
        $node = $this->crawler->filter('duration');
        return $node->count() > 0 ? (int) $node->innerText() : 0;
    }

    public function isDot() : bool
    {
        return $this->dotCount() > 0;
    }

    public function dotCount() : int
    {
        $node = $this->crawler->filter('dot');
        return $node->count();
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

    public function isGrace() : bool
    {
        return $this->crawler->filter('grace')->count() > 0;
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

    public function pitchStep() : ?string
    {
        $step = $this->crawler->filter('pitch > step');
        return $step->count() > 0 ? strtolower($step->innerText()) : null;
    }

    public function pitchOctave() : int
    {
        $octave = $this->crawler->filter('pitch > octave');
        return $octave->count() > 0 ? (int) $octave->innerText() : 0;
    }

    public function unpitchedStep() : ?string
    {
        $step = $this->crawler->filter('unpitched > display-step');
        return $step->count() > 0 ? strtolower($step->innerText()) : null;
    }

    public function unpitchedOctave() : ?int
    {
        $octave = $this->crawler->filter('unpitched > display-octave');
        return $octave->count() > 0 ? (int) $octave->innerText() : 0;
    }

    public function timeModification() : ?array
    {
        $timeModification = $this->crawler->filter('time-modification');

        if ($timeModification->count() > 0) {
            $actualNotes = (int) $timeModification->filter('actual-notes')->innerText();
            $normalNotes = (int) $timeModification->filter('normal-notes')->innerText();
            return [ $actualNotes, $normalNotes ];
        }

        return null;
    }

}