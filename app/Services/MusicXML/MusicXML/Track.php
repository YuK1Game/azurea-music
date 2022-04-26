<?php
namespace App\Services\MusicXML\MusicXML;

use Illuminate\Support\Collection;

class Track
{
    protected Collection $notesList;

    public function __construct(Collection $notesList)
    {
        $this->notesList = $notesList;
    }

    public function flatten() : Collection
    {
        return $this->notesList->flatten();
    }

}