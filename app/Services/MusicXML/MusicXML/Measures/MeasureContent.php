<?php
namespace App\Services\MusicXML\MusicXML\Measures;

use InvalidArgumentException;

abstract class MeasureContent
{
    protected function getTextByFilterPath(string $filterPath) : ?string
    {
        try {
            return $this->crawler->filterXPath($filterPath)->text();

        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(sprintf("%s\n%s", $e->getMessage(), $this->crawler->outerHtml()));
        }
    }

}