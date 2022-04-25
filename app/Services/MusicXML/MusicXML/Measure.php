<?php
namespace App\Services\MusicXML\MusicXML;

use App\Services\MusicXML\MusicXML\Note;
use App\Services\MusicXML\MusicXML\Backup;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler as DOMCrawler;
use DOMElement;

class Measure
{
    protected $element;

    public function __construct(DOMElement $domElement)
    {
        $this->crawler = new DOMCrawler($domElement);
    }

    public function notes() : Collection
    {
        $data = collect();

        foreach ($this->crawler->filterXPath('//note|//backup') as $dom) {
            switch ($dom->nodeName) {
                case 'note' :
                    $data->push(new Note($dom)); 
                    break;
                case 'backup':
                    $data->push(new Backup($dom)); 
                    break;
            }
        }

        return $data;
    }

    public function getBackupIndexes() : Collection
    {
        $backupIndexes = collect();

        foreach ($this->notes() as $index => $note) {
            if ($note instanceof Backup) {
                $backupIndexes->push($index);
            }
        }

        return $backupIndexes;
    }

    public function trackA() : Collection
    {
        if ($this->getBackupIndexes()->count() >= 1) {
            $from = 0;
            $to = $this->getBackupIndexes()->get(0);
            return $this->notes()->slice($from, $from - $to);
        }
        return $this->notes();
    }

    public function trackB() : Collection
    {
        if ($this->getBackupIndexes()->count() >= 1) {
            $from = $this->getBackupIndexes()->get(0) + 1;
            $to = $this->getBackupIndexes()->get(1) ?? $this->notes()->count() - 1;
            return $this->notes()->slice($from, $from - $to);
        }
        return collect();
    }


}