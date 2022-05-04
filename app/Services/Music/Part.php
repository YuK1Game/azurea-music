<?php
namespace App\Services\Music;

use App\Services\Music\Parts\MeasureChunk;
use App\Services\MusicXML\MusicXML\Measure;
use Collator;
use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use Illuminate\Support\Collection;

class Part extends Node implements NodeInterface
{
    public function measures() : Collection
    {
        $parts = collect();

        $this->crawler->filter('measure')->each(function(DOMCrawler $crawler) use($parts) {
            $parts->push(new Parts\Measure($crawler, $this));
        });

        return $parts;
    }

    public function maxDuration() : int
    {
        return $this->measures()->max(function(Parts\Measure $measure) {
            return $measure->childrenChunk()->max(function(MeasureChunk $measureChunk) {
                return $measureChunk->totalNoteDuration();
            });
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
        return $this->measures()->map(function(Parts\Measure $measure, $index) use($number) {
            return $measure->childrenChunk()->get($number);
        });
    }

    public function tracks() : Collection
    {
        return collect([
            'trackA' => $this->trackA(),
            'trackB' => $this->trackB(),
            'trackC' => $this->trackC(),
        ]);
    }

}