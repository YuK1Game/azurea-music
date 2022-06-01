<?php
namespace App\Services\Music;

class ScorePart extends Node implements NodeInterface
{
    public function getId() : string
    {
        return $this->crawler->attr('id');
    }

    public function part() : Part
    {
        $partDom = $this->parentCrawler()->filter(sprintf('part[id="%s"]', $this->getId()));
        return new Part($partDom, $this);
    }

    public function partName() : ?string
    {
        $partNameDom = $this->crawler->filter('part-name');
        return $partNameDom->count() > 0 ? $partNameDom->innerText() : null;
    }

}