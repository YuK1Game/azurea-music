<?php
namespace App\Services\Music\Parts;

use App\Services\Music\Parts\Measures\MeasureChildrenInterface;
use Illuminate\Support\Collection;

class MeasureChunk
{
    protected Collection $measureChunk;

    public function __construct(Collection $measureChunk)
    {
        $this->measureChunk = $measureChunk;
    }

    public function totalNoteDuration() : int
    {
        return $this->measureChunk->sum(function(MeasureChildrenInterface $measureChildren) {
            if ($measureChildren instanceof Measures\Note) {
                return $measureChildren->duration();
            }
            return 0;
        });
    }

}