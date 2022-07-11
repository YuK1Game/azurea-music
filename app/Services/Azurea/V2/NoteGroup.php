<?php
namespace App\Services\Azurea\V2;

use Illuminate\Support\Collection;

use App\Services\Azurea\V2\Note as AzureaNote;

class NoteGroup extends Collection
{
    protected $noteGroup;

    public function getCurrentMeasureNumber() : int
    {
        $firstNote = $this->first();
        return $firstNote->getCurrentMeasureNumber();
    }

    public function getCode() : string
    {
        $code = $this->isArpeggiate() ? $this->getArpeggiateCodes() : $this->getCodes();
        return $code->join('');
    }

    public function getCodes() : Collection
    {
        return $this->map(function(AzureaNote $azureaNote) {
            return $azureaNote->getCode();
        });
    }

    public function getArpeggiateCodes() : Collection
    {
        $mainDuration = $this->duration() / 2;
        $childDuration = ($this->duration() - $mainDuration) / ($this->noteCount() - 1);

        return $this->values()->map(function(AzureaNote $azureaNote, int $index) use($mainDuration, $childDuration) {
            $azureaNote->setCustomDuration($index === 0 ? $mainDuration : $childDuration);
            return $azureaNote->getCode();
        });
    }

    public function noteCount() : int
    {
        return $this->count();
    }

    public function duration() : int
    {
        return $this->firstNote()->duration();
    }

    public function isArpeggiate() : bool
    {
        return false;
        return $this->firstNote()->arpeggiate() && $this->noteCount() > 1;
    }

    protected function firstNote() : AzureaNote
    {
        return $this->first();
    }

}
