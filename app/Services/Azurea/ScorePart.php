<?php
namespace App\Services\Azurea;

use App\Services\Music\Part as MusicPart;
use App\Services\Music\ScorePart as MusicScorePart;

use App\Services\Azurea\Part as AzureaPart;

class ScorePart
{
    protected MusicScorePart $scorePart;

    protected int $scorePartIndex;

    public function __construct(MusicScorePart $scorePart, int $scorePartIndex)
    {
        $this->scorePart = $scorePart;
        $this->scorePartIndex = $scorePartIndex;
    }

    public function part() : AzureaPart
    {
        return new AzureaPart($this->scorePart->part());
    }

    public function partName() : ?string
    {
        return $this->scorePart->partName();
    }

    public function exportCode() : void
    {
        echo join(PHP_EOL, [
            str_repeat('=', 100),
            sprintf('ScorePart [%d]', $this->scorePartIndex + 1),
            str_repeat('=', 100),
        ]) . PHP_EOL;

        $part = $this->part();
        $part->exportCode();
    }

}