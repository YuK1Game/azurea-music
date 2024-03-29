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

    public function getCode() : Collection
    {
        $defaultCodes = $this->getCodes();

        try {
            $arpeggiateCodes = $this->getArpeggiateCodes();

        } catch (\Exception $e) {
            logger()->warning($e->getMessage());
            return $defaultCodes->join('');
        }

        return $this->isArpeggiate() ? $arpeggiateCodes->values() : $defaultCodes->values();
    }

    public function getCodes() : Collection
    {
        return $this->map(function(AzureaNote $azureaNote) {
            return $azureaNote->getCode();
        });
    }

    public function getArpeggiateCodes() : Collection
    {
        $wholeDuration = $this->firstNote()->getWholeDuration();
        $baseDuration  = $this->firstNote()->duration();
        $subDuration   = $wholeDuration / 32;
        $mainDuration  = $baseDuration - ($subDuration * ($this->noteCount() - 1));

        $codes = $this->reverse()->values()->map(function(AzureaNote $azureaNote, int $index) use($mainDuration, $subDuration) {
            $azureaNote->setCustomDuration($index === 0 ? $mainDuration : $subDuration);
            return $azureaNote->getCode();
        })
        ->reverse()
        ->values();

        return $codes;
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
        return $this->firstNote()->arpeggiate() && $this->noteCount() > 1;
    }

    protected function firstNote() : AzureaNote
    {
        return $this->first();
    }

    public function json() : Collection
    {
        return $this->map(function(AzureaNote $azureaNote) {
            return $azureaNote->json();
        });
    }

}
