<?php
namespace App\Services\Music\Parts;

use App\Services\Music\{ Node, NodeInterface };
use App\Services\Music\Parts\Measures\MeasureChildrenInterface;
use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use Illuminate\Support\Collection;
use App\Services\Music\Parts\Measures\MeasureKey;
use App\Services\Music\Parts\Measures\Note;

class Measure extends Node implements NodeInterface
{
    protected ?Collection $children;

    protected ?int $totalDuration = null;

    public function __construct(DOMCrawler $crawler, NodeInterface $parentNode)
    {
        parent::__construct($crawler, $parentNode);

        $this->initChildren();
    }

    private function initChildren() : void
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

        $this->children = $children;
    }

    public function totalDuration() : int
    {
        if ( ! $this->totalDuration) {
            $this->totalDuration = $this->notes()->sum(function(Note $note) {
                if ( ! $note->isChord()) {
                    return $note->duration();
                }
                return 0;
            });
        }

        return $this->totalDuration;
    }

    public function time() : array
    {
        return [
            (int) $this->crawler->filter('attributes > time > beats')->innerText(),
            (int) $this->crawler->filter('attributes > time > beat-type')->innerText(),
        ];
    }

    public function measureKey() : MeasureKey
    {
        $node = $this->crawler->filter('attributes > key > fifths');
        $index = $node->count() > 0 ? $node->innerText() : null;
        return MeasureKey::factory($index);
    }
    
    public function narrowDownChildrenByIndex(int $index) : void
    {
        $this->children = $this->children
            ->chunkWhile(function(MeasureChildrenInterface $measureChildren) {
                return $measureChildren instanceof Measures\Note;
            })
            ->get($index) ?? collect();
    }

    public function hasNotes() : bool
    {
        return $this->notes()->count() > 0;
    }

    public function notes() : ?Collection
    {
        return $this->children->filter(function(MeasureChildrenInterface $measureChildren) {
            return $measureChildren instanceof Note;
        });
    }

}