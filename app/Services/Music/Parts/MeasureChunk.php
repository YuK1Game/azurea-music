<?php
namespace App\Services\Music\Parts;

use App\Services\Music\Parts\Measures\MeasureChildrenInterface;
use App\Services\Music\Parts\Measures\Note;
use Illuminate\Support\Collection;

class MeasureChunk
{
    protected Collection $measureChunk;

    protected ?int $totalNoteDuration = null;

    public function __construct(Collection $measureChunk)
    {
        $this->measureChunk = $measureChunk;
    }

    protected function initTotalNoteDuration() : void
    {
        if( ! $this->totalNoteDuration) {
            $this->totalNoteDuration = $this->measureChunk->sum(function(MeasureChildrenInterface $measureChildren) {
                if ($measureChildren instanceof Measures\Note && ! $measureChildren->isChord()) {
                    return $measureChildren->duration();
                }
                return 0;
            });
        }
    }

    public function totalNoteDuration() : ?int
    {
        $this->initTotalNoteDuration();
        return $this->totalNoteDuration;
    }

    public function notes() : Collection
    {
        return $this->measureChunk->map(function(MeasureChildrenInterface $measureChildren) {
            if ($measureChildren instanceof Note) {
                $measureChildren->setTotalNoteDuration($this->totalNoteDuration());
                return $measureChildren;
            }
            return null;
        })
        ->filter();
    }

}