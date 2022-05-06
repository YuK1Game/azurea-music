<?php
namespace App\Services\Azurea;

use App\Services\Music\Part as MusicPart;
use App\Services\Music\ScorePart as MusicScorePart;

use App\Services\Azurea\Part as AzureaPart;

class ScorePart
{
    protected MusicScorePart $scorePart;

    public function __construct(MusicScorePart $scorePart)
    {
        $this->scorePart = $scorePart;
    }

    public function part() : AzureaPart
    {
        return new AzureaPart($this->scorePart->part());
    }

    public function exportCode() : void
    {
        $part = $this->part();
        $part->exportCode();
    }

}