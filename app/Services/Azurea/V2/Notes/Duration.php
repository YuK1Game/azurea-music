<?php
namespace App\Services\Azurea\V2\Notes;

use App\Services\Azurea\V2\Note as AzureaNote;
use Illuminate\Support\Collection;

class Duration
{
    protected int $duration;

    protected int $division;

    protected int $beat;

    protected int $beatType;

    public function __construct(int $duration, int $division, int $beat, int $beatType)
    {
        $this->duration = $duration;
        $this->division = $division;
        $this->beat     = $beat;
        $this->beatType = $beatType;
    }

    public function wholeDuration() : int
    {
        return $this->division * 4;
    }

    public function isNaturalDuration() : bool
    {
        return $this->wholeDuration() % $this->duration === 0;
    }

    public function normalizedDuration() : int
    {
        if ($this->isNaturalDuration()) {
            return $this->duration;
        }

        $notes = $this->createNoteDurations();
        $currentNote = $notes->first(function($data) {
            return $this->duration >= $data->get('duration');
        });

        return $currentNote->get('duration');
    }

    public function duration() : int
    {
        return $this->wholeDuration() / $this->normalizedDuration();
    }

    public function createNoteDurations() : Collection
    {
        $durations = collect();
        $whole = $this->wholeDuration();
        $value = 1;

        do {
            $duration = $whole / $value;
            $duration >= 1 && $durations->push(collect(['type' => $value, 'duration' => $duration ]));
            $value *= 2;
        } while($duration >= 1);
        
        return $durations;
    }

    public function dotCount() : int
    {
        $ratio = $this->duration / $this->normalizedDuration();

        switch ($ratio) {
            case 1.00  : return 0;
            case 1.50  : return 1;
            case 1.75  : return 2;
            default :
                throw new \Exception(sprintf('Invalid duration. [%s] ratio [%s]', $this->duration, $ratio));
        }
    }
    
}