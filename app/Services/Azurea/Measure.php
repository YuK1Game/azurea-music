<?php
namespace App\Services\Azurea;

use App\Services\Music\Parts\Measure as MusicMeasure;
use App\Services\Music\Parts\Measures\MeasureKey as MusicMeasureKey;
use App\Services\Music\Parts\Measures\Note as MusicNote;

use App\Services\Azurea\Note as AzureaNote;

use Illuminate\Support\Collection;

class Measure
{
    protected MusicMeasure $musicMeasure;

    protected ?MusicMeasureKey $musicMeasureKey = null;

    public function __construct(MusicMeasure $musicMeasure)
    {
        $this->musicMeasure = $musicMeasure;
    }

    public function setMeasureKey(?MusicMeasureKey $musicMeasureKey)
    {
        $this->musicMeasureKey = $musicMeasureKey;
    }

    public function getNotes() : ?Collection
    {
        return $this->_getNotes() ?? $this->_getBlankNotes();
    }

    public function totalLength() : int
    {
        return $this->getNotes()->sum(function(AzureaNote $azureaNote) {
            return $azureaNote->isChord() ? 0 : $azureaNote->length();
        });
    }

    protected function _getNotes() : ?Collection
    {
        $measureDuration = $this->musicMeasure->totalDuration();

        return $this->musicMeasure->hasNotes() ? $this->musicMeasure->notes()->map(function(MusicNote $note) use($measureDuration) {
            $azureaNote = new AzureaNote($note, $measureDuration);
            $azureaNote->setMeasureKey($this->musicMeasureKey);
            return $azureaNote;
        }) : null;
    }

    protected function _getBlankNotes() : ?Collection
    {
        $azureaNote = new AzureaNote(null, 0);
        // $azureaNote->setNoteLength($this->musicMeasure->totalLength());
        return collect([ $azureaNote ]);
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->{ $name }(...$arguments);
        }
        if (method_exists($this->musicMeasure, $name)) {
            return $this->musicMeasure->{ $name }(...$arguments);
        }
    }
}