<?php
namespace App\Services\Music\Parts;

use App\Services\Music\{ Node, NodeInterface };
use App\Services\Music\Parts\Measures\MeasureChildrenInterface;
use App\Services\Music\Parts\MeasureChunk;
use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use Illuminate\Support\Collection;

class Measure extends Node implements NodeInterface
{
    public function time() : array
    {
        return [
            (int) $this->crawler->filter('time > beats')->innerText(),
            (int) $this->crawler->filter('time > beat-type')->innerText(),
        ];
    }

    public function children() : Collection
    {
        $children = collect();

        $this->crawler->filter('note,backup')->each(function(DOMCrawler $crawler) use($children) {
            switch ($crawler->nodeName()) {
                case 'note' :
                    $children->push(new Measures\Note($crawler, $this));
                    break;

                case 'backup':
                    $children->push(new Measures\Backup($crawler, $this));
                    break;
            }
        });

        return $children;
    }

    public function childrenChunk() : Collection
    {
        return $this->children()
            ->chunkWhile(function(MeasureChildrenInterface $measureChild) {
                return $measureChild instanceof Measures\Note;
            })
            ->map(function(Collection $chunk) {
                return new MeasureChunk($chunk);
            });
    }

}