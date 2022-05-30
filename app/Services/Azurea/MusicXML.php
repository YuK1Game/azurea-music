<?php
namespace App\Services\Azurea;

use App\Services\Music\{
    MusicXML as Music_MusicXML,
    ScorePart as Music_ScorePart,
};
use App\Services\Azurea\ScorePart as AzureaScorePart;
use Illuminate\Support\Collection;

class MusicXML
{
    protected Music_MusicXML $musicXml;

    protected Collection $tempos;

    public function __construct(Music_MusicXML $musicXml)
    {
        $this->musicXml = $musicXml;
    }

    protected function getScoreParts()
    {
        $music = $this->musicXml->music();
        $scoreParts = $music->scoreParts();
        return $scoreParts->map(function(Music_ScorePart $scorePart, int $scorePartIndex) {
            return new AzureaScorePart($scorePart, $scorePartIndex);
        });
    }

    public function exportCode()
    {
        $scoreParts = $this->getScoreParts();
        $this->exportPartCode($scoreParts);
    }

    public function exportPartCode(Collection $scoreParts) : void
    {
        $scoreParts->each(function(AzureaScorePart $azureaScorePart, int $index) {
            $part = $azureaScorePart->part();

            echo join(PHP_EOL, [
                str_repeat('=', 100),
                sprintf('ScorePart [%d]', $index + 1),
                str_repeat('=', 100),
            ]) . PHP_EOL;

            if ($part->getTempos()->count() > 0) {
                $this->tempos = $part->getTempos();
            }

            if ($this->tempos->count() > 0) {
                $part->setTempos($this->tempos);
            }

            $part->exportCode();
        });

    }

}