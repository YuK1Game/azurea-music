<?php
namespace App\Services\Azurea;

use App\Services\Music\ScorePart as MusicScorePart;

class ScorePart
{
    protected MusicScorePart $scorePart;

    public function __construct(MusicScorePart $scorePart)
    {
        $this->scorePart = $scorePart;
    }

}