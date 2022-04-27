<?php
namespace App\Services\MusicXML\MusicXML\Measures;

use InvalidArgumentException;

abstract class MeasureContent
{
    protected function getTextByFilterPath(string $filterPath) : ?string
    {
        try {
            $dom = $this->crawler->filterXPath($filterPath);
            return $dom->count() > 0 ? $dom->text() : null;

        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(sprintf("%s filterXPath [%s]\n%s", $e->getMessage(), $filterPath, $this->crawler->outerHtml()));
        }
    }

    protected function hasDomByFilterPath(string $filterPath) : bool
    {
        return $this->crawler->filterXPath($filterPath)->count() > 0;
    }

}