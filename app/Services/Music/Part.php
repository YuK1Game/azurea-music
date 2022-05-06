<?php
namespace App\Services\Music;

use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use Illuminate\Support\Collection;

class Part extends Node implements NodeInterface
{
    protected ?Collection $measures = null;

    public function keys() : array
    {
        $firstMeasure = $this->measures()->first();
        return $firstMeasure->keys();
    }

    public function measures() : Collection
    {
        if ( ! $this->measures) {
            $measures = collect();

            $this->crawler->filter('measure')->each(function(DOMCrawler $crawler) use($measures) {
                $measures->push(new Parts\Measure($crawler, $this));
            });
    
            $this->measures = $measures;
        }
 
        return $this->measures;
    }

    public function maxDuration() : int
    {
        return $this->trackA()->max(function(Parts\Measure $measure) {
            return $measure->totalDuration();
        });
    }

    public function trackA() : Collection
    {
        return $this->getTrack(0);
    }

    public function trackB() : Collection
    {
        return $this->getTrack(1);
    }

    public function trackC() : Collection
    {
        return $this->getTrack(2);
    }

    public function getTrack(int $number) : Collection
    {
        return $this->measures()->map(function(Parts\Measure $measure) use($number) {
            $newMeasure = clone $measure;
            $newMeasure->narrowDownChildrenByIndex($number);
            return $newMeasure;
        });
    }

    public function tracks() : Collection
    {
        return collect([
            $this->trackA(),
            $this->trackB(),
            $this->trackC(),
        ]);
    }

}