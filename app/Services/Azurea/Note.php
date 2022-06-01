<?php
namespace App\Services\Azurea;

use App\Services\Music\Parts\Measures\Note as MusicNote;
use App\Services\Azurea\Notes\NotePitchTable;
use App\Services\Music\Parts\Measures\MeasureKey;
use Illuminate\Support\Collection;

class Note
{
    protected ?MusicNote $musicNote;

    protected int $baseDuration = 0;

    protected ?int $noteDuration = null;

    protected ?Note $prevNote;

    protected int $sharpCount;

    protected int $flatCount;

    protected MeasureKey $measureKey;

    protected Collection $measureSharpPitches;
    
    protected Collection $measureFlatPitches;

    protected Collection $measureNaturalPitches;

    public function __construct(?MusicNote $musicNote, int $measureTotalDuration)
    {
        $this->musicNote = $musicNote;
        $this->measureTotalDuration = $measureTotalDuration;

        $this->sharpCount = ($this->musicNote && $this->musicNote->isSharp()) ? 1 : 0;
        $this->flatCount = ($this->musicNote && $this->musicNote->isFlat()) ? 1 : 0;
    }

    public function setNoteDuration(int $noteDuration) : void
    {
        $this->noteDuration = $noteDuration;
    }

    public function getNoteDuration() : ?int
    {
        return $this->noteDuration;
    }

    public function setPrevNote(?Note $prevNote) : void
    {
        $this->prevNote = $prevNote;
    }

    public function setMeasureKey(?MeasureKey $measureKey) : void
    {
        $this->measureKey = $measureKey ?? new MeasureKey(0);
    }

    public function setMeasureSharpPitches(Collection $pitchSharps)
    {
        $this->measureSharpPitches = $pitchSharps;
    }

    public function setMeasureFlatPitches(Collection $pitchFlats)
    {
        $this->measureFlatPitches = $pitchFlats;
    }

    public function setMeasureMaturalPitches(Collection $pitchNaturals)
    {
        $this->measureNaturalPitches = $pitchNaturals;
    }

    public function isBlank() : bool
    {
        return ! $this->musicNote;
    }

    public function isSamePitchPrevNote() : bool
    {
        return $this->prevNote && $this->prevNote->pitch() === $this->pitch();
    }

    public function code() : string
    {
        if ($this->isBlank()) {
            return $this->rest();
        }

        if ($this->musicNote->isGrace()) {
            return '';
        }

        return $this->musicNote->isRest() ? $this->rest() : $this->step();
    }

    public function codeDebug() : string
    {
        if ($this->isBlank() || $this->musicNote->isRest()) {
            return $this->rest();
        }

        if ($this->musicNote->isGrace()) {
            return '';
        }

        $pitchStep = $this->musicNote->pitchStep();

        $list = [
            'c' => 'ド',
            'd' => 'レ',
            'e' => 'ミ',
            'f' => 'ファ',
            'g' => 'ソ',
            'a' => 'ラ',
            'b' => 'シ',
        ];

        $text = sprintf('%s(%d,%d)', collect($list)->get($pitchStep), $this->getSharpCount(), $this->getFlatCount());

        if ( ! $this->musicNote->isChord()) {
            $text = ' ' . $text;
        }

        return $text;
    }

    public function rest() : string
    {
        return sprintf('r%s', $this->duration());
    }

    public function step() : string
    {
        list($pitchStep, $pitchOctave) = $this->pitch();
        
        $pitchCode = sprintf('o%d%s', $pitchOctave, $pitchStep);
        $step = sprintf('%s%s', $pitchCode, $this->duration());

        if ($this->musicNote->isChord()) {
            $step = ':' . $step;
        }

        if ($this->musicNote->isTieEnd() && $this->isSamePitchPrevNote()) {
            return $this->musicNote->isChord() ? '' : $this->rest();
        }

        return $step;
    }

    public function pitch() : array
    {
        $pitchStep = $this->musicNote->pitchStep();
        $pitchOctave = $this->musicNote->pitchOctave();

        if ( ! $pitchStep && ! $pitchOctave) {
            return $this->unpitched();
        }

        if ( ! $this->musicNote->isNatural() && ! $this->hasMeasureNatural()) {
            list($pitchStep, $pitchOctave) = NotePitchTable::addPitch($pitchStep, $pitchOctave, $this->getSharpCount());
            list($pitchStep, $pitchOctave) = NotePitchTable::subPitch($pitchStep, $pitchOctave, $this->getFlatCount());
        }

        return [ $pitchStep, $pitchOctave ];
    }

    public function unpitched() : array
    {
        $pitchStep = $this->musicNote->unpitchedStep();
        $pitchOctave = $this->musicNote->unpitchedOctave();
        $pitch = sprintf('o%d%s', $pitchOctave, $pitchStep);

        switch ($pitch) {
            case 'o4d' : return ['a+', 4]; // ハイハット・シンバル(足)
            case 'o4f' : return ['c',  4]; // バス・ドラム
            case 'o4a' : return ['e',  4]; // フロアタム
            case 'o5c' : return ['c+', 4]; // スネア・ドラム
            case 'o5d' : return ['d+', 4]; // ロータム
            case 'o5e' : return ['d',  4]; // ハイタム
            case 'o5f' : return ['a',  4]; // ライドシンバル
            case 'o5g' : return ['f',  4]; // クローズハット
            case 'o5a' : return ['g',  4]; // クラッシュシンバル
            
            default : throw new \Exception(sprintf('Pitch not fount [%s]', $pitch));
        }
    }

    public function duration() : string
    {
        list($baseDuration, $isDottedDuration) = $this->baseDuration();

        $baseDuration = $baseDuration;

        if ($isDottedDuration) {
            $baseDuration .= '.';
        }

        return $baseDuration;
    }

    public function defaultPitch() : string
    {
        return sprintf('o%d%s', $this->musicNote->pitchOctave(), $this->musicNote->pitchStep());
    }

    protected function hasMeasureSharp() : bool
    {
        return $this->measureSharpPitches->contains($this->defaultPitch());
    }

    protected function hasMeasureFlat() : bool
    {
        return $this->measureFlatPitches->contains($this->defaultPitch());
    }

    protected function hasMeasureNatural() : bool
    {
        return $this->measureNaturalPitches->contains($this->defaultPitch());
    }

    protected function getSharpCount() : int
    {
        $count = $this->musicNote->isSharp() || $this->hasMeasureSharp() ? 1 : 0;
        $count += $this->measureKey->isSharp($this->musicNote->pitchStep()) ? 1 : 0;

        return $count;
    }

    protected function getFlatCount() : int
    {
        $count = $this->musicNote->isFlat() || $this->hasMeasureFlat() ? 1 : 0;
        $count += $this->measureKey->isFlat($this->musicNote->pitchStep()) ? 1 : 0;
        return $count;
    }

    protected function baseDuration() : array
    {
        try {
            $noteDuration = $this->getNoteDuration() ?? $this->musicNote->duration();
            $x = $noteDuration / $this->measureTotalDuration;
            $y = fmod(1, $x);
    
            $isDottedDuration = $y > 0;
            $duration = 1 / ($isDottedDuration ? ($x / 1.5) : $x);

        } catch (\Exception $e) {
            return [ 0, false ];
        }


        return [ $duration, $isDottedDuration ];
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->{ $name }(...$arguments);
        }
        if (method_exists($this->musicNote, $name)) {
            return $this->musicNote->{ $name }(...$arguments);
        }
    }
}