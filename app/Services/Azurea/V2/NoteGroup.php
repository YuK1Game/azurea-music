<?php
namespace App\Services\Azurea\V2;

use Illuminate\Support\Collection;

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
        return $this->getCodes()->join('');
    }

    public function getCodes() : Collection
    {
        return $this->map(function(Note $azureaNote) {
            return $azureaNote->getCode();
        });
    }

}
